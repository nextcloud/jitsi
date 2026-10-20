const assert = require('node:assert/strict')
const fs = require('node:fs')
const path = require('node:path')
const { test } = require('node:test')
const vm = require('node:vm')
const { transformSync } = require('@babel/core')
const { parseComponent } = require('vue-template-compiler')

// Exercise the actual component methods without mounting the settings UI.
function settings(api) {
	const source = fs.readFileSync(path.join(__dirname, '../../src/Admin.vue'), 'utf8')
	const script = parseComponent(source).script.content
	const { code } = transformSync(script, {
		babelrc: false,
		configFile: false,
		presets: [['@babel/preset-env', { targets: { node: 'current' } }]],
	})
	const context = {
		exports: {},
		require: () => ({}),
		OCP: { AppConfig: api },
	}
	vm.runInNewContext(code, context)
	const component = context.exports.default
	return { ...component.data(), ...component.methods, t: (_app, message) => message }
}

test('loads the JSON configuration data returned by Nextcloud 35', async () => {
	const component = settings({
		getValue(app, key, fallback, callbacks) {
			assert.equal(app, 'jitsi')
			assert.equal(key, 'jitsi_server_url')
			assert.equal(fallback, '')
			callbacks.success({ data: 'https://meet.example.org/' })
		},
	})
	assert.equal(await component.loadSetting('jitsi_server_url'), 'https://meet.example.org/')
})

test('preserves empty values and the string checkbox setting in JSON', async () => {
	for (const value of ['', '0', '1']) {
		const component = settings({ getValue: (_app, _key, _fallback, callbacks) => callbacks.success({ data: value }) })
		assert.equal(await component.loadSetting('display_join_using_the_jitsi_app', '1'), value)
	}
})

test('still loads XML configuration responses from older Nextcloud versions', async () => {
	const component = settings({
		getValue: (_app, _key, _fallback, callbacks) => callbacks.success({
			querySelector: selector => selector === 'status'
				? { textContent: 'ok' }
				: { firstElementChild: { textContent: 'legacy-secret' } },
		}),
	})
	assert.equal(await component.loadSetting('jwt_secret'), 'legacy-secret')
})

test('uses the default for an empty XML data element', async () => {
	const component = settings({
		getValue: (_app, _key, _fallback, callbacks) => callbacks.success({
			querySelector: selector => selector === 'status' ? { textContent: 'ok' } : { firstElementChild: null },
		}),
	})
	assert.equal(await component.loadSetting('display_join_using_the_jitsi_app', '1'), '1')
})

test('rejects malformed or failed responses instead of silently losing settings', async () => {
	for (const response of [null, {}, { data: { message: 'Forbidden' } }, { querySelector: () => ({ textContent: 'failure' }) }]) {
		const component = settings({ getValue: (_app, _key, _fallback, callbacks) => callbacks.success(response) })
		await assert.rejects(component.loadSetting('jwt_secret'))
		assert.equal(component.errorMessage, 'Failed to load settings')
	}
})

test('propagates request errors', async () => {
	const error = new Error('Forbidden')
	const component = settings({ getValue: (_app, _key, _fallback, callbacks) => callbacks.error(error) })
	await assert.rejects(component.loadSetting('jwt_secret'), error)
	assert.equal(component.errorMessage, 'Failed to load settings')
})

test('saves values through the core API, preserving password confirmation', async () => {
	const component = settings({
		setValue(app, key, value, callbacks) {
			assert.equal(app, 'jitsi')
			assert.equal(key, 'jwt_secret')
			assert.equal(value, 'new-secret')
			callbacks.success()
		},
	})
	await component.updateSetting('jwt_secret', 'new-secret')
})
