<template>
    <div>
        <div class="tol-system-check-title">
            {{ t('jitsi', 'System check') }}
        </div>
        <div v-if="!loading && !permissionDenied" class="tol-system-checks-container">
            <div class="tol-system-check tol-system-check--first">
                <CameraTest
                    :permission-denied="permissionDenied"
                    :cameras="cameras"
                    v-on="$listeners" />
            </div>
            <div class="tol-system-check">
                <MicTest
                    :permission-denied="permissionDenied"
                    :microphones="microphones"
                    v-on="$listeners" />
                <hr class="tol-check-divider">
                <SpeakerTest
                    :permission-denied="permissionDenied"
                    :browser="browser"
                    :speakers="speakers"
                    v-on="$listeners" />
                <hr class="tol-check-divider">
                <BrowserTest :browser="browser" />
            </div>
        </div>
        <div
            v-if="!loading && permissionDenied"
            class="tol-system-checks-permission-denied">
            <div class="tol-system-checks-permission-denied__message-container">
                <CheckStatusIcon class="tol-system-checks-permission-denied__icon" status="error" />
                <div class="tol-system-checks-permission-denied__title">
                    {{ t('jitsi', 'No camera / microphone access') }}
                </div>
            </div>
            <div class="tol-system-checks-permission-denied__message-container">
                {{ t('jitsi', 'Click on the icon on the left in the browser bar next to the URL and allow access:') }}<br>
            </div>
            <div style="text-align: center;">
                <img
                    class="tol-system-checks-permission-denied__allow-img"
                    :src="allowSrc">
            </div>
            <div v-if="$root.helpLink">
                <div class="tol-system-checks-permission-denied__title2">
                    {{ t('jitsi', 'Doesn\'t work?') }}
                </div>
                <a
                    :href="$root.helpLink + '#permissions'"
                    class="tol-system-checks-permission-denied__button button secondary">
                    {{ t('jitsi', 'Click here for trouhleshooting help') }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>

import Bowser from 'bowser'
import BrowserTest from './BrowserTest'
import CameraTest from './CameraTest'
import MicTest from './MicTest'
import SpeakerTest from './SpeakerTest'
import CheckStatusIcon from './CheckStatusIcon'
import { generateFilePath } from '@nextcloud/router'

async function askPermissions(constraints, vm, notFoundCallback) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia(constraints)
        stream.getTracks().forEach((track) => {
            track.stop()
        })
    } catch (err) {
        console.log(`[jitsi] getUserMedia() error.name: ${err.name}`)
        console.log(`[jitsi] getUserMedia() error.message: ${err.message}`)

        if (err.name === 'NotFoundError' && notFoundCallback) {
            await notFoundCallback()
        }

        if (err.name === 'NotAllowedError') {
            vm.permissionDenied = true
            vm.$root.$emit('jitsi.device_permission_denied')
        }
    }

}

export default {
    name: 'SystemTest',
    components: {
        CheckStatusIcon,
        SpeakerTest,
        MicTest,
        CameraTest,
        BrowserTest,
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
        },
    },
    async created() {
        this.browser = Bowser.getParser(window.navigator.userAgent)

        await Promise.race([
            // safeguard because some browsers do not return
            new Promise(resolve => setTimeout(resolve, 500)),
            this.askPermissions(),
        ])

        try {
            await this.queryDevices()
        } catch (err) {
            console.log('Error detecting devices')
            console.log(err)
        }

        this.loading = false

        this.$root.$on('tol-refresh-devices', () => {
            // console.log('[jitsi] tol-refresh-devices')
            this.queryDevices()
        })
    },
    methods: {
        async askPermissions() {
            await askPermissions(
                { audio: true, video: true},
                this,
                async () => askPermissions({ audio: true }, this)
            )
        },
        async queryDevices() {
            const devices = await navigator.mediaDevices.enumerateDevices()
            const cameras = []
            const microphones = []
            const speakers = []

            devices.forEach((device) => {
                // console.log(`[jitsi] device`)
                // console.log(device)

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
        },
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
