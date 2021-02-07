<template>
	<div>
		<div class="tol-system-check-title">
			Systemprüfung
		</div>
		<div class="tol-system-checks-container" v-if="!loading && !permissionDenied">
			<div class="tol-system-check tol-system-check--first">
				<CameraTest
					:permission-denied="permissionDenied"
					:cameras="cameras"
					v-on="$listeners">
				</CameraTest>
			</div>
			<div class="tol-system-check">
				<MicTest
					:permission-denied="permissionDenied"
					:microphones="microphones"
					v-on="$listeners">
				</MicTest>
				<hr class="tol-check-divider">
				<SpeakerTest
					:permission-denied="permissionDenied"
					:browser="browser"
					:speakers="speakers"
					v-on="$listeners">
				</SpeakerTest>
				<hr class="tol-check-divider">
				<BrowserTest :browser="browser"></BrowserTest>
			</div>
		</div>
		<div
			class="tol-system-checks-permission-denied"
			v-if="!loading && permissionDenied">
			<div class="tol-system-checks-permission-denied__message-container">
				<CheckStatusIcon class="tol-system-checks-permission-denied__icon" status="error"></CheckStatusIcon>
				<div class="tol-system-checks-permission-denied__title">
					Kein Kamera-/ Mikrofonzugriff
				</div>
			</div>
			<div class="tol-system-checks-permission-denied__message-container">
				Klicke in der Browser-Zeile auf das Icon links neben der URL und erlaube den Zugriff:<br>
			</div>
			<div style="text-align: center;">
				<img
					class="tol-system-checks-permission-denied__allow-img"
					:src="allowSrc">
			</div>
			<div v-if="$root.helpLink">
				<div class="tol-system-checks-permission-denied__title2">
					Klappt nicht?
				</div>
				<a
					:href="$root.helpLink + '#permissions'"
					class="tol-system-checks-permission-denied__button button secondary">
					Hier findest du Hilfe zur Fehlerbehebung
				</a>
			</div>
		</div>
	</div>
</template>

<script>

import Bowser from 'bowser'
import BrowserTest from "./BrowserTest"
import CameraTest from "./CameraTest";
import MicTest from "./MicTest";
import SpeakerTest from "./SpeakerTest";
import CheckStatusIcon from "./CheckStatusIcon";
import {generateFilePath} from "@nextcloud/router";

export default {
	name: 'SystemTest',
	components: {
		CheckStatusIcon,
		SpeakerTest,
		MicTest,
		CameraTest,
		BrowserTest
	},
	data() {
		return {
			browser: undefined,
			selectedCamera: '',
			selectedMicrophone: '',
			cameras: [],
			microphones: [],
			speakers: [],
			loading: true,
			permissionDenied: false,
		}
	},
	computed: {
		allowSrc() {
			return generateFilePath('jitsi', 'img', 'allow.png')
		}
	},
	methods: {
		async askPermissions() {
			try {
				const stream = await navigator.mediaDevices.getUserMedia({audio: true, video: true})
				stream.getTracks().forEach((track) => {
					track.stop()
				})
			} catch (err) {
				console.log(`[tol] getUserMedia() error: ${err.message}`)

				if (err.name === 'NotAllowedError') {
					this.permissionDenied = true
					this.$root.$emit('tol-permission-denied')
				}
			}
		},
		async queryDevices() {
			const devices = await navigator.mediaDevices.enumerateDevices()
			const cameras = []
			const microphones = []
			const speakers = []

			devices.forEach((device) => {
				console.log(`[tol] device`)
				console.log(device)

				if (device.kind === 'videoinput') {
					cameras.push(device)
				}

				if (device.kind === 'audioinput' && device.label.toLocaleLowerCase().includes('monitor of') === false) {
					microphones.push(device)
				}

				if (device.kind === 'audiooutput') {
					speakers.push(device)
				}
			})

			this.cameras = cameras
			this.microphones = microphones
			this.speakers = speakers
		}
	},
	async created() {
		this.browser = Bowser.getParser(window.navigator.userAgent)
		await this.askPermissions()
		await this.queryDevices()
		this.loading = false

		this.$root.$on('tol-refresh-devices', () => {
			console.log('[tol] tol-refresh-devices')
			this.queryDevices()
		})
	},
}
</script>

<style scoped>

.tol-system-check-title {
	font-size: 18px;
	margin-bottom: 16px;
	text-align: center;
}

.tol-system-checks-container {
	display: flex;
	flex-direction: column;
}

.tol-system-check {
	border: 1px solid var(--color-border);
	padding: 16px;
	width: 100%;
}

.tol-system-check--first {
	margin-bottom: 16px;
	margin-right: 0;
}

.tol-system-checks-permission-denied {
	border: 1px solid var(--color-border);
	display: flex;
	flex-direction: column;
	margin-left: auto;
	margin-right: auto;
	padding: 16px;
}

.tol-system-checks-permission-denied__message-container {
	align-items: center;
	display: flex;
	margin-bottom: 16px;
	margin-left: auto;
	margin-right: auto;
}

.tol-system-checks-permission-denied__title {
	font-size: 18px;
	font-weight: bold;
	line-height: 1.2;
}

.tol-system-checks-permission-denied__title2 {
	color: var(--color-text-lighter);
	font-size: 24px;
	font-weight: bold;
	line-height: 1.2;
	margin-bottom: 8px;
	text-align: center;
}

.tol-system-checks-permission-denied__icon {
	flex-shrink: 0;
	margin-right: 8px;
}

.tol-system-checks-permission-denied__allow-img {
	margin-bottom: 8px;
	max-width: 100%;
}

.tol-system-checks-permission-denied__button {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

@media only screen and (min-width: 768px) {
	.tol-system-checks-container {
		flex-direction: row;
	}

	.tol-system-check {
		width: 50%;
	}

	.tol-system-check--first {
		margin-bottom: 0;
		margin-right: 16px;
	}
}

</style>