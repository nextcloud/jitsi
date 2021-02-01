<template>
	<div>
		<div class="tol-check">
			<div class="check-result-icon-container">
				<CheckStatusIcon :status="status"></CheckStatusIcon>
			</div>
			<div class="tol-check-right">
				<div class="tol-check-title-row">
					<div class="tol-check-title">
						Browser
						<span v-if="isNonOptimalBrowser"> nicht optimal</span>
						<span v-if="isNotWorkingBrowser"> nicht untertützt</span>
					</div>
				</div>
				<div class="tol-check-text">
					{{ deviceType }}: {{ browserName }} ({{ browserVersion }})
				</div>
				<div
					v-if="isNonOptimalBrowser"
					class="tol-check-text">
					Bild- und Tonqualität könnten schlecht sein.<br>
					Wir empfehlen den neusten Chrome oder Chromium.
				</div>
				<a
					class="button tol-check-help-button"
					:href="$root.helpLink + '#browser'"
					v-if="$root.helpLink && status !== 'pending' && status !== 'ok'">
					<img
						class="tol-check-help-button__icon"
						src="/index.php/svg/core/actions/info?color=000">
					<div>Hilfe</div>
				</a>
			</div>
		</div>
	</div>
</template>

<script>

import CheckStatusIcon from "./CheckStatusIcon";

const OPTIMAL_BROWSERS = {
	chrome: '>=78',
	edge: '>=79',
	safari: '>=10',
}

const NON_OPTIMAL_BROWSERS = {
	firefox: '>=78',
}

const NOT_WORKING_BROWSERS = {
	'internet explorer': '*',
}

export default {
	name: "BrowserTest",
	components: {CheckStatusIcon},
	props: [
		'browser',
	],
	data() {
		return {
			deviceType: '',
			browserName: '',
			browserVersion: '',
			isOptimalBrowser: false,
			isNonOptimalBrowser: false,
			isNotWorkingBrowser: false,
			status: 'pending',
		}
	},
	computed: {},
	async created() {
		const deviceType = this.browser.getPlatformType()
		this.deviceType = deviceType.charAt(0).toUpperCase() + deviceType.slice(1)
		this.browserName = this.browser.getBrowserName()
		this.browserVersion = this.browser.getBrowserVersion()

		this.isOptimalBrowser = this.browser.satisfies(OPTIMAL_BROWSERS)
		this.isNotWorkingBrowser = this.browser.satisfies(NOT_WORKING_BROWSERS)
		this.isNonOptimalBrowser = this.browser.satisfies(NON_OPTIMAL_BROWSERS)

		if (this.isOptimalBrowser) {
			this.status = 'ok'
			return
		}

		if (this.isNotWorkingBrowser) {
			this.status = 'error'
			return
		}

		this.status = 'warning'

		this.$root.$emit('tol-browser-status', this.status)
	},
}
</script>

<style scoped>

@import "../css/check.css";

</style>
