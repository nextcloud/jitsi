<template>
    <div class="tol-check">
        <div class="check-result-icon-container">
            <CheckStatusIcon :status="status" />
        </div>
        <div class="tol-check-right">
            <div class="tol-check-title-row">
                <div class="tol-check-title">
                    {{ t('jitsi', 'Camera') }}
                </div>
                <a
                    v-if="$root.helpLink && status !== 'pending' && status !== 'ok'"
                    class="tol-check-title-help"
                    :href="$root.helpLink + '#camera'">{{ t('jitsi', 'Help') }}</a>
            </div>
            <div>
                <select
                    v-model="selectedCameraId"
                    class="tol-check-select"
                    :disabled="cameras.length === 0">
                    <option
                        v-for="device in cameras"
                        :key="device.deviceId"
                        :value="device.deviceId">
                        {{ device.label }}
                    </option>
                </select>
                <div class="tol-check-video-container">
                    <video
                        ref="video"
                        class="tol-check-video"
                        playsinline
                        autoplay />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import CheckStatusIcon from './CheckStatusIcon'

export default {
    name: 'CameraTest',
    components: { CheckStatusIcon },
    props: [
        'cameras',
        'permissionDenied',
    ],
    data() {
        return {
            selectedCamera: undefined,
            stream: undefined,
            status: 'pending',
        }
    },
    computed: {
        selectedCameraId: {
            get() {
                return this.selectedCamera ? this.selectedCamera.deviceId : undefined
            },
            async set(id) {
                this.selectedCamera = this.cameras.find(ele => ele.deviceId === id)
                this.$emit('camera-selected', this.selectedCamera)
                localStorage.setItem('tol-preferred-camera', this.selectedCamera.deviceId)

                await this.startPreview()
            },
        },
    },
    created() {
        this.$root.$on('stop-streams', () => {
            this.stop()
            this.$root.$emit('cam-stopped')
        })

        this.$root.$on('resume-preview', () => {
            this.startPreview()
        })
    },
    mounted() {
        if (this.permissionDenied || !this.cameras || this.cameras.length === 0) {
            this.status = 'error'
            return
        }

        const preferredCameraId = localStorage.getItem('tol-preferred-camera')

        if (preferredCameraId) {
            const cam = this.cameras.find(ele => ele.deviceId === preferredCameraId)
            if (cam) {
                this.selectedCameraId = cam.deviceId
                return
            }
        }

        this.selectedCameraId = this.cameras[0].deviceId
    },
    methods: {
        async startPreview() {
            this.stop()

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { deviceId: this.selectedCamera.deviceId },
                })
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    this.status = 'error'
                }
                this.status = 'error'
                return
            }

            this.$refs.video.srcObject = this.stream
            this.status = 'ok'
        },
        stop() {
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

.tol-check-video-container {
	background-color: var(--color-border);
	padding-top: 75%;
	position: relative;
	width: 100%;
}

.tol-check-video {
	height: 100%;
	left: 0;
	object-fit: cover;
	position: absolute;
	top: 0;
	width: 100%;
}

</style>
