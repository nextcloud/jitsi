<template>
	<div class="tol-check">
		<div class="check-result-icon-container">
			<CheckStatusIcon :status="status" />
		</div>
		<div class="tol-check-right">
			<div class="tol-check-title-row">
				<div class="tol-check-title">
					{{ t('jitsi', 'Audio output') }}
				</div>
				<a
					v-if="$root.helpLink && status !== 'pending' && status !== 'ok'"
					class="tol-check-title-help"
					:href="$root.helpLink + '#speaker'">{{ t('jitsi', 'Help') }}</a>
			</div>
			<div>
				<select
					v-if="showSpeakerSelect"
					v-model="selectedSpeakerId"
					class="tol-check-select"
					:disabled="speakers.length === 0">
					<option
						v-for="device in speakers"
						:key="device.deviceId"
						:value="device.deviceId">
						{{ device.label }}
					</option>
				</select>
				<div
					class="tol-play-button button secondary"
					@click="playTestSound">
					<img
						class="tol-play-button__icon"
						:src="playSrc"> {{ t('jitsi', 'Play test sound') }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>

import CheckStatusIcon from './CheckStatusIcon'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'SpeakerTest',
	components: { CheckStatusIcon },
	props: {
		browser: Object,
		speakers: Array,
		permissionDenied: Boolean,
	},
	data() {
		return {
			status: 'pending',
			selectedSpeaker: undefined,
			stream: undefined,
			audioContext: undefined,
		}
	},
	computed: {
		showSpeakerSelect() {
			return this.browser.getBrowserName(true) !== 'firefox'
				&& this.browser.getBrowserName(true) !== 'safari'
		},
		selectedSpeakerId: {
			get() {
				return this.selectedSpeaker ? this.selectedSpeaker.deviceId : undefined
			},
			async set(id) {
				this.selectedSpeaker = this.speakers.find(ele => ele.deviceId === id)
				this.$emit('speaker-selected', this.selectedSpeaker)
				localStorage.setItem('tol-preferred-speaker', this.selectedSpeaker.deviceId)
			},
		},
		playSrc() {
			return this.link('/svg/core/actions/play?color=000000')
		},
	},
	mounted() {
		if (this.browser.getBrowserName(true) === 'firefox') {
			this.status = 'sound'
			return
		}

		if (this.permissionDenied) {
			this.status = 'error'
			return
		}

		const preferredSpeakerId = localStorage.getItem('tol-preferred-speaker')

		if (preferredSpeakerId) {
			const speaker = this.speakers.find(ele => ele.deviceId === preferredSpeakerId)
			if (speaker) {
				this.selectedSpeaker = speaker
				this.status = 'ok'
				return
			}
		}

		if (this.speakers && this.speakers.length > 0) {
			const defaultSpeaker = this.speakers.find(ele => ele.label.includes('default'))
			if (defaultSpeaker) {
				this.selectedSpeakerId = defaultSpeaker.deviceId
			} else {
				this.selectedSpeakerId = this.speakers[0].deviceId
			}
			this.status = 'ok'
		}
	},
	methods: {
		playTestSound() {
			const testFileUrl = generateUrl('/apps/jitsi/assets/sounds/test.wav')
			const audio = new Audio(testFileUrl)

			if (this.browser.getBrowserName(true) !== 'firefox') {
				audio.setSinkId(this.selectedSpeaker.deviceId)
			}

			audio.play()
		},
	},
}

</script>

<style scoped>

@import "../css/check.css";

.tol-play-button {
	align-items: center;
	display: flex;
	justify-content: center;
}

.tol-play-button__icon {
	margin-right: 8px;
}

</style>
