<template>
	<div class="tol-check">
		<div class="check-result-icon-container">
			<CheckStatusIcon :status="status"></CheckStatusIcon>
		</div>
		<div class="tol-check-right">
			<div class="tol-check-title-row">
				<div class="tol-check-title">
					{{ t('jitsi', 'Microphone') }}
				</div>
				<a
					class="tol-check-title-help"
					:href="$root.helpLink + '#microphone'"
					v-if="$root.helpLink && status !== 'pending' && status !== 'ok'">{{ t('jitsi', 'Help') }}</a>
			</div>
			<div>
				<select
					v-if="!loading"
					class="tol-check-select"
					v-model="selectedMicrophoneId"
					:disabled="microphones.length === 0">
					<option
						v-for="device in microphones"
						:key="device.deviceId"
						:value="device.deviceId">
						{{ device.label }}
					</option>
				</select>
				<div
					ref="meter"
					class="meter">
					<img
						class="meter__icon"
						src="/index.php/svg/core/actions/audio?color=cccccc">
				</div>
			</div>
		</div>
	</div>
</template>

<script>

import CheckStatusIcon from './CheckStatusIcon'

export default {
	name: 'MicTest',
	components: {CheckStatusIcon},
	props: {
		microphones: Array,
		permissionDenied: Boolean,
	},
	data() {
		return {
			status: 'pending',
			selectedMicrophone: undefined,
			audioContext: undefined,
			stream: undefined,
			loading: true,
			audioProcessTimeout: undefined,
		}
	},
	created() {
		this.$root.$on('stop-streams', async () => {
			await this.stop()
			this.$root.$emit('mic-stopped')
		})

		this.$root.$on('resume-preview', () => {
			this.startPreview()
		})
	},
	mounted() {
		if (this.permissionDenied || !this.microphones || this.microphones.length === 0) {
			this.status = 'error'
			return
		}

		const preferredMicrophoneId = localStorage.getItem('tol-preferred-microphone');

		if (preferredMicrophoneId) {
			const mic = this.microphones.find(ele => ele.deviceId === preferredMicrophoneId)
			if (mic) {
				this.selectedMicrophoneId = mic.deviceId
				this.status = 'sound'
				this.loading = false
				return
			}
		}

		const defaultMicrophone = this.microphones.find(ele => ele.label.includes('default'))
		if (defaultMicrophone) {
			this.selectedMicrophoneId = defaultMicrophone.deviceId
		} else {
			this.selectedMicrophoneId = this.microphones[0].deviceId
		}
		this.status = 'sound'
		this.loading = false
	},
	computed: {
		selectedMicrophoneId: {
			get() {
				return this.selectedMicrophone ? this.selectedMicrophone.deviceId : undefined
			},
			async set(microphoneId) {
				this.selectedMicrophone = this.microphones.find(mic => mic.deviceId === microphoneId)
				this.$emit('microphoneSelected', this.selectedMicrophone)
				localStorage.setItem('tol-preferred-microphone', this.selectedMicrophone.deviceId)

				await this.startPreview()
			}
		}
	},
	methods: {
		async startPreview() {
			await this.stop()

			try {
				this.stream = await navigator.mediaDevices.getUserMedia({
					audio: {deviceId: this.selectedMicrophone.deviceId}
				})
			} catch (err) {
				// console.log(err)

				if (err.name === 'NotAllowedError') {
					this.status = 'error'
					return
				}

				this.status = 'error'
				return
			}

			this.audioContext = new AudioContext()
			const mediaStreamSource = this.audioContext.createMediaStreamSource(this.stream)
			const processor = this.audioContext.createScriptProcessor(2048, 1, 1)

			// mediaStreamSource.connect(this.audioContext.destination)
			mediaStreamSource.connect(processor)
			processor.connect(this.audioContext.destination)

			processor.onaudioprocess = this.debouncedOnAudioProcess

			this.$root.$emit('tol-refresh-devices')
		},
		debouncedOnAudioProcess(event) {
			if (this.audioProcessTimeout) {
				return
			}

			this.onAudioProcess(event)

			this.audioProcessTimeout = setTimeout(() => {
				this.audioProcessTimeout = undefined
			}, 50)
		},
		onAudioProcess(event) {
			const inputData = event.inputBuffer.getChannelData(0)
			const inputDataLength = inputData.length
			let total = 0

			for (let i = 0; i < inputDataLength; i++) {
				total += Math.abs(inputData[i++])
			}

			const rms = Math.sqrt(total / inputDataLength)
			const audioLevel = Math.round(rms * 100)

			if (this.$refs.meter && this.$refs.meter.classList && this.$refs.meter) {
				if (audioLevel > 10) {
					this.$refs.meter.classList.add('meter--active')
				} else {
					this.$refs.meter.classList.remove('meter--active')
				}
			}

			if (audioLevel > 10 && this.status !== 'ok') {
				this.status = 'ok'
			}
		},
		async stop() {
			if (this.audioContext) {
				await this.audioContext.close()
				this.audioContext = undefined
			}

			if (this.stream) {
				this.stream.getTracks().forEach((track) => {
					track.stop()
				})
			}
		},
	},
}

</script>

<style scoped>

@import "../css/check.css";

.meter {
	align-items: center;
	border: 2px solid var(--color-border);
	display: flex;
	height: 64px;
	justify-content: center;
}

.meter--active {
	background-color: #059669;
}

.meter__icon {
	height: 32px;
	width: 32px;
}

</style>
