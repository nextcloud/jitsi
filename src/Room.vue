<template>
	<div class="app-content">
		<link
			rel="preload"
			href="/index.php/svg/core/actions/audio?color=#000"
			as="image">
		<link
			rel="preload"
			href="/index.php/svg/core/actions/audio-off?color=#000"
			as="image">
		<link
			rel="preload"
			href="/index.php/svg/core/actions/video?color=#000"
			as="image">
		<link
			rel="preload"
			href="/index.php/svg/core/actions/video-off?color=#000"
			as="image">

		<Breadcrumbs v-if="user">
			<Breadcrumb :disable-drop="true" title="Home" :href="appHomeUrl"/>
			<Breadcrumb :disable-drop="true" :title="room.name"/>
		</Breadcrumbs>
		<div
			class="room"
			:style="{ 'padding-top': user ? '46px' : '80px' }"
			v-if="room">
			<div class="room__sub-title">
				Konferenz
			</div>
			<h1 class="room__title">
				{{ room.name }}
			</h1>
			<div class="room__join-browser-section">
				<div v-if="conferenceDone" class="room__done-info">
					Konferenz verlassen
				</div>
				<div
					v-if="!systemOk"
					class="room__system-test-summary room__system-test-summary--warning">
					<div class="room__system-test-summary__title__row">
						<img
							class="room__system-test-summary__icon"
							src="/index.php/svg/core/actions/error?color=ea580c">
						<div class="room__system-test-summary__title">
							{{ t('jitsi', 'Problems detected') }}
						</div>
					</div>
					<div class="room__system-test-summary__text">
						<ul class="tol-ul-icons">
							<li
								v-if="browserStatus === 'warning'">
								<b>Browser nicht optimal:</b><br>
								Bild- und Tonqualität könnten schlecht sein.<br>
								Empfohlen wird der neuste <b>Chrome</b>/<b>Chromium</b>.
							</li>
							<li v-if="browserStatus === 'error'">Browser nicht unterstützt</li>
						</ul>
					</div>
					<div class="room__system-test-summary__actions">
						<a
							class="button secondary"
							href="#system-test">
							Systemprüfung anzeigen
						</a>
					</div>
				</div>
				<div
					class="room__username"
					v-if="!user">
					<label
						class="room__username-label">
						Benutzername:
					</label><br>
					<input
						class="room__username-input"
						type="text"
						maxlength="20"
						v-model="userName">
				</div>
				<button
					class="primary room__join-button--browser"
					@click="joinBrowser"
					:disabled="joining">
					Hier geht's rein
				</button>

				<div class="room__options">
					<label class="room__option">
						<input
							class="room__option__checkbox"
							type="checkbox"
							v-model="microphoneInActive"> Stummgeschaltet beginnen
					</label>
					<label class="room__option">
						<input
							class="room__option__checkbox"
							type="checkbox"
							v-model="cameraInActive"> Kamera zu Beginn aus
					</label>
				</div>
			</div>

			<SystemTest
				id="system-test"
				class="tol-system-test-section"
				@microphoneSelected="onMicrophoneSelected"
				@cameraSelected="onCameraSelected"
				@speakerSelected="onSpeakerSelected">
			</SystemTest>

			<div
				class="room__join-app-toggle"
				@click="showJoinApp = !showJoinApp">
				Mit der Jitsi-App teilnehmen (Beta)
				<img
					class="room__join-app-toggle-icon"
					:class="{ 'room__join-app-toggle-icon--up': showJoinApp }"
					src="/index.php/svg/core/actions/caret?color=000000">
			</div>
			<div
				class="room__join-app-section"
				v-if="showJoinApp">
				<ol class="room__app-instructions">
					<li class="room__app-instructions-item">
						<a
							target="_blank"
							href="https://github.com/jitsi/jitsi-meet-electron#installation">
							Hier App herunterladen ↗
						</a>
					</li>
					<li class="room__app-instructions-item">
						<button
							v-if="!joinLink"
							@click="createJoinLink">
							Teilnahme-Link erstellen
						</button>
						<span v-if="joinLink" class="room__join-link-container">
							<input
								class="room__join-link-input"
								:value="joinLink"
								readonly>
							<Actions ref="copyLinkActions">
								<ActionLink
									:href="joinLink"
									:icon="copied && copySuccess ? 'icon-checkmark-color' : 'icon-clippy'"
									@click.stop.prevent="copyLink">
									{{ clipboardTooltip }}
								</ActionLink>
							</Actions>
						</span>
					</li>
					<li class="room__app-instructions-item">
						Innerhalb von 60 Sekunden<br>
						in die Jitsi-App einfügen
					</li>
				</ol>
			</div>
		</div>
		<div
			ref="conferenceContainer"
			class="conference-container"
			:class="{ 'conference-container--running': conferenceRunning }"/>
		<a
			ref="linkHelper"
			class="link-helper"
			:href="linkHelperUrl"></a>
	</div>
</template>

<script>

import {generateUrl} from '@nextcloud/router'
import axios from '@nextcloud/axios'
import JitsiMeetExternalAPI from './external_api'
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import Breadcrumb from '@nextcloud/vue/dist/Components/Breadcrumb'
import Breadcrumbs from '@nextcloud/vue/dist/Components/Breadcrumbs'
import SystemTest from './components/SystemTest'

export default {
	name: 'Room',
	components: {
		ActionLink,
		Actions,
		Breadcrumb,
		Breadcrumbs,
		SystemTest,
	},
	data() {
		return {
			joining: false,
			conferenceRunning: false,
			conferenceDone: false,
			room: null,
			user: null,
			userName: '',
			linkHelperUrl: '',
			joinLink: '',
			copied: false,
			copySuccess: false,
			showJoinApp: false,
			cameraInActive: false,
			microphoneInActive: false,
			serverUrl: null,
			serverHost: null,
			selectedCamera: null,
			selectedMicrophone: null,
			selectedSpeaker: null,
			permissionDenied: false,
			browserStatus: null
		}
	},
	computed: {
		appHomeUrl() {
			return window.location.protocol + '//'
				+ window.location.host
				+ generateUrl('/apps/jitsi')
		},
		clipboardTooltip() {
			if (this.copied) {
				return this.copySuccess
					? t('jjitsi', 'Link copied')
					: t('jjitsi', 'Cannot copy, please copy the link manually')
			}
			return t('jjitsi', 'Copy to clipboard')
		},
		systemOk() {
			if (this.browserStatus && this.browserStatus !== 'ok') {
				return false
			}

			return true
		}
	},
	async created() {
		this.$root.$on('tol-permission-denied', () => {
			this.permissionDenied = true
		})

		this.$root.$on('tol-browser-status', (status) => {
			console.log(`[tol] tol-browser-status ${status}`)
			this.browserStatus = status
		})

		const jitsiEle = document.getElementById('jitsi')
		this.serverUrl = jitsiEle.dataset.serverUrl
		this.$root.helpLink = jitsiEle.dataset.helpLink
		const url = new URL(this.serverUrl)
		this.serverHost = url.host

		const roomResponse = await axios.get(generateUrl(`/apps/jitsi/api/rooms/${this.extractRoomId()}`))
		const userResponse = await axios.get(generateUrl('/apps/jitsi/api/user'))

		this.user = userResponse.data.user
		this.room = roomResponse.data
	},
	methods: {
		onCameraSelected(camera) {
			this.selectedCamera = camera
		},
		onMicrophoneSelected(microphone) {
			this.selectedMicrophone = microphone
		},
		onSpeakerSelected(speaker) {
			this.selectedSpeaker = speaker
		},
		async createJoinLink() {
			const token = await this.issueToken()
			this.joinLink = `${this.serverUrl}${this.room.publicId}?jwt=${token}`
			await this.copyLink()
			setTimeout(() => {
				this.joinLink = ''
			}, 60 * 1000)
		},
		async copyLink() {
			try {
				await this.$copyText(this.joinLink)
				// focus and show the tooltip
				this.$refs.copyLinkActions.$el.focus()
				this.copySuccess = true
				this.copied = true
			} catch (error) {
				this.copySuccess = false
				this.copied = true
			} finally {
				setTimeout(() => {
					this.copySuccess = false
					this.copied = false
				}, 4000)
			}
		},
		async joinApp() {
			const token = await this.issueToken()
			this.linkHelperUrl = `jitsi-meet://${this.serverHost}/${this.room.publicId}?jwt=${token}`
			setTimeout(() => {
				this.$refs.linkHelper.click()
			}, 0)
		},
		async joinBrowser() {
			if (this.joining) {
				return;
			}

			this.joining = true

			await this.stopStreams()

			const token = await this.issueToken()
			const options = {
				parentNode: this.$refs.conferenceContainer,
				width: '100%',
				height: '100%',
				roomName: this.room.publicId,
				jwt: token,
				devices: {}
			}

			const configOverwrite = {}

			if (this.microphoneInActive) {
				configOverwrite.startWithAudioMuted = true
			}

			if (this.cameraInActive) {
				configOverwrite.startWithVideoMuted = true
			}

			if (this.selectedCamera) {
				console.log(`[tol] preferred camera: ${this.selectedCamera.label}`)
				options.devices.videoInput = this.selectedCamera.label
			}

			if (this.selectedMicrophone) {
				console.log(`[tol] preferred microphone: ${this.selectedMicrophone.label}`)
				options.devices.audioInput = this.selectedMicrophone.label
			}

			if (this.selectedSpeaker) {
				console.log(`[tol] preferred speaker: ${this.selectedSpeaker.label}`)
				options.devices.audioOutput = this.selectedSpeaker.label
			}

			options.configOverwrite = configOverwrite

			this.conferenceRunning = true
			const api = new JitsiMeetExternalAPI(this.serverHost, options)
			api.executeCommand('subject', this.room.name)
			api.addEventListener('readyToClose', () => {
				this.joining = false
				api.dispose()
				this.conferenceRunning = false
				this.conferenceDone = true
				this.$root.$emit('resume-preview')
			})
		},
		async stopStreams() {
			return new Promise((resolve) => {
				let micStopped = false
				let camStopped = false

				this.$root.$once('mic-stopped', () => {
					micStopped = true
					if (camStopped) {
						resolve()
					}
				})

				this.$root.$once('cam-stopped', () => {
					camStopped = true
					if (micStopped) {
						resolve()
					}
				})

				this.$root.$emit('stop-streams');
			})
		},
		async issueToken() {
			const data = {
				displayName: this.user ? this.user.displayName : this.userName,
			}
			const url = generateUrl(`/apps/jitsi/api/rooms/${this.room.publicId}/tokens`)
			const response = await axios.post(url, data)
			return response.data.token
		},
		extractRoomId() {
			return window.location.href.split('/').slice(-2)[0]
		},
	},
}
</script>

<style scoped>

.room {
	align-items: center;
	display: flex;
	flex-direction: column;
	padding: 48px 16px 100px;
}

.room__sub-title {
	font-size: 16px;
	margin-bottom: 8px;
	text-align: center;
}

.room__title {
	color: var(--color-text-lighter);
	font-size: 48px;
	line-height: 1.2;
	margin-bottom: 80px;
	text-align: center;
}

.room__username {
	margin: 0 0 16px;
}

.room__username-input {
	width: 250px;
}

.room__join-button--browser {
	display: block;
	font-size: 16px;
	margin: 0 auto 32px;
	padding: 16px 32px;
	width: 250px;
}

.room__join-browser-section {
	align-items: center;
	display: inline-flex;
	flex-direction: column;
	margin-bottom: 80px;
}

.room__join-app-section {
	padding-top: 32px;
}

.room__join-app-toggle {
	cursor: pointer;
}

.room__join-app-toggle-icon {
	height: 24px;
	position: relative;
	top: 8px;
}

.room__join-app-toggle-icon--up {
	transform: rotate(180deg);
}

.room__app-instructions {
	display: inline-block;
	text-align: left;
}

.room__app-instructions-item {
	list-style-type: decimal;
}

.room__join-link-container {
	align-items: center;
	display: flex;
}

.room__join-link-input {
	margin-right: 8px;
	width: 200px;
}

.app-content {
	width: 100%;
}

@media only screen and (min-width: 576px) {
	.app-content {
		margin-left: auto;
		margin-right: auto;
		max-width: 992px;
	}
}

.conference-container {
	bottom: 0;
	display: none;
	left: 0;
	position: fixed;
	top: 0;
	width: 100%;
	z-index: 2500;
}

.conference-container--running {
	display: block;
}

.link-helper {
	display: none;
}

.room__options {
	margin-bottom: 16px;
}

.room__option {
	align-items: center;
	display: flex;
	margin-bottom: 8px;
	user-select: none;
}

.room__option__checkbox {
	margin: 0 4px 0 0;
	min-height: auto;
}

.tol-system-test-section {
	margin-bottom: 32px;
	width: 100%;
}

.room__done-info {
	color: #059669;
	font-size: 24px;
	font-weight: bold;
	margin-bottom: 16px;
}

.room__system-test-summary {
	border-radius: 4px;
	margin-bottom: 16px;
	padding: 16px;
	max-width: 400px;
}

.room__system-test-summary--warning {
	background-color: #eca700;
}

.room__system-test-summary__title__row {
	align-items: center;
	display: flex;
	justify-content: start;
	margin-bottom: 8px;
}

.room__system-test-summary__icon {
	height: 32px;
	margin-right: 8px;
	width: 32px;
}

.room__system-test-summary__title {
	color: #222;
	font-size: 24px;
	font-weight: bold;
}

.room__system-test-summary__text {
	color: #222;
	margin-bottom: 16px;
}

.room__system-test-summary__actions {
	text-align: center;
}

.tol-ul-icons {
	padding-left: 24px;
}

.tol-ul-icons li {
	list-style-type: disc;
}

</style>
