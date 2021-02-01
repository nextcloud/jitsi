<template>
	<div>
		<form @submit.prevent="submit">
			<fieldset :disabled="saving">
				<SettingsSection title="Jitsi">
					<div v-if="loading">
						{{ t('jitsi_settings', 'loading…') }}
					</div>
					<div v-if="!loading">
						<p>
							<label
								for="jitsi_server_url"
								class="label">
								{{ t('jitsi_settings', 'Server URL (required)') }}
							</label>
							<input
								id="jitsi_server_url"
								v-model="serverUrl"
								class="input"
								type="text">
						</p>
						<p>
							<label
								for="jitsi_jwt_secret"
								class="label">
								{{ t('jitsi_settings', 'JWT Secret (required)') }}
							</label>
							<input
								id="jitsi_jwt_secret"
								v-model="jwtSecret"
								class="input"
								type="text">
						</p>
						<p>
							<label
								for="jitsi_help_link"
								class="label">
								{{ t('jitsi_settings', 'Help link (optional)') }}
							</label>
							<input
								id="jitsi_help_link"
								v-model="helpLink"
								class="input"
								type="text">
						</p>
						<p>
							<button
								type="submit"
								class="primary"
								:disabled="saving">
								{{ t('jitsi_settings', 'save') }}
							</button>
							<span
								v-if="!saving && saved"
								class="msg success">
								{{ t('jitsi_settings', 'saved') }}
							</span>
							<span
								v-if="saving"
								class="msg">
								{{ t('jitsi_settings', 'saving…') }}
							</span>
						</p>
					</div>
				</SettingsSection>
			</fieldset>
		</form>
	</div>
</template>

<script>

import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'

export default {
	name: 'Admin',
	components: {
		SettingsSection,
	},
	data() {
		return {
			loading: true,
			saving: false,
			saved: false,
			errorMessage: '',
			jwtSecret: '',
			serverUrl: '',
			helpLink: '',
		}
	},
	async created() {
		this.jwtSecret = await this.loadSetting('jwt_secret')
		this.serverUrl = await this.loadSetting('server_url')
		this.helpLink = await this.loadSetting('help_link')
		this.loading = false
	},
	methods: {
		async submit() {
			this.saving = true
			this.saved = false

			await Promise.all([
				this.updateSetting('server_url', this.serverUrl),
				this.updateSetting('jwt_secret', this.jwtSecret),
				this.updateSetting('help_link', this.helpLink),
			])

			this.saving = false
			this.saved = true
		},
		async updateSetting(name, value) {
			try {
				await new Promise((resolve, reject) =>
					OCP.AppConfig.setValue('jitsi', name, value, {
						success: resolve,
						error: reject,
					})
				)
			} catch (e) {
				this.error = this.t('jitsi', 'Failed to save settings')
				throw e
			}
		},
		async loadSetting(name) {
			try {
				const resDocument = await new Promise((resolve, reject) =>
					OCP.AppConfig.getValue('jitsi', name, null, {
						success: resolve,
						error: reject,
					})
				)
				if (resDocument.querySelector('status').textContent !== 'ok') {
					this.errorMessage = this.t('jitsi', 'Failed to load settings')
					console.error('Failed request', resDocument)
					return
				}
				const dataEl = resDocument.querySelector('data')
				return dataEl.firstElementChild.textContent
			} catch (e) {
				this.errorMessage = this.t('jitsi', 'Failed to load settings')
				throw e
			}
		},
	},
}
</script>

<style scoped>
.label {
	display: block;
	width: 100%;
}

.input {
	display: block;
	width: 100%;
}

.msg {
	display: inline-block;
	margin-top: 10px;
}

@media only screen and (min-width: 576px) {
	.label {
		display: inline-block;
		margin-right: 10px;
		width: 175px;
	}

	.input {
		display: inline-block;
		width: 400px;
	}

	button.primary {
		margin-left: 113px;
	}
}

</style>
