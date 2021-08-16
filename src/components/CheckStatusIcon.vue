<template>
	<div
		class="check-result-icon"
		:class="statusIconClass">
		<img
			class="check-result-icon__img"
			:src="statusIconSrc">
	</div>
</template>

<script>

export default {
	name: "CheckStatusIcon",
	props: [
		'status',
	],
	computed: {
		statusIconSrc() {
			switch (this.status) {
				case 'pending':
					return this.link('/svg/core/actions/more?color=ffffff')
				case 'error':
					return this.link('/svg/core/actions/close?color=ffffff')
				case 'warning':
					return this.link('/svg/core/actions/details?color=ffffff')
				case 'ok':
					return this.link('/svg/core/actions/checkmark?color=ffffff')
				case 'sound':
					return this.link('/svg/core/actions/sound?color=ffffff')
			}
		},
		statusIconClass() {
			return `check-result-icon--${this.status}`;
		},
	},
}
</script>

<style scoped>

.check-result-icon {
	align-items: center;
	border-radius: 50%;
	display: flex;
	height: 16px;
	justify-content: center;
	margin-top: 4px;
	width: 16px;
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

.check-result-icon__img {
	height: 12px;
	width: 12px;
}

</style>
