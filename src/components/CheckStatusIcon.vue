<template>
	<div
		class="check-result-icon"
		:class="statusIconClass">
		<CheckBoldIcon v-if="status === 'ok'" />
		<TimerSandEmptyIcon v-else-if="status === 'pending'" />
		<CloseThickIcon v-else-if="status === 'error'" />
		<ExclamationThickIcon v-else-if="status === 'warning'" />
		<VolumeHighIcon v-else-if="status === 'sound'" />
		<CloseThickIcon v-else />
	</div>
</template>

<script>

import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue'
import CloseThickIcon from 'vue-material-design-icons/CloseThick.vue'
import ExclamationThickIcon from 'vue-material-design-icons/ExclamationThick.vue'
import TimerSandEmptyIcon from 'vue-material-design-icons/TimerSandEmpty.vue'
import VolumeHighIcon from 'vue-material-design-icons/VolumeHigh.vue'

export default {
	name: 'CheckStatusIcon',
	components: {
		CheckBoldIcon,
		CloseThickIcon,
		ExclamationThickIcon,
		TimerSandEmptyIcon,
		VolumeHighIcon,
	},
	props: [
		'status',
	],
	computed: {
		statusIconClass() {
			return `check-result-icon--${this.status}`
		},
	},
}
</script>

<style scoped>

.check-result-icon {
	align-items: center;
	border-radius: 50%;
	color: var(--color-primary-text);
	display: flex;
	justify-content: center;
	margin-top: 4px;
    padding: 4px;
    position: relative;
    top: -3px;
}

.check-result-icon--pending,
.check-result-icon--sound {
	background-color: #94A3B8;
}

.check-result-icon--ok {
	background-color: #059669;
}

.check-result-icon--error {
	background-color: #DC2626;
}

.check-result-icon--warning {
	background-color: #D97706;
}

</style>
