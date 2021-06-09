<template>
	<div class="empty-room-list">
		<div class="empty-room-list__text">
			{{ t('jitsi', 'No conference rooms yet') }}
		</div>
		<form @submit.prevent="create">
			<label class="empty-room-list__new__label">
				{{ t('jitsi', 'Create the first room:') }}
			</label>
			<div class="empty-room-list__new__input-container">
				<input
					ref="roomNameInput"
					v-model="name"
					class="empty-room-list__new__input"
                    :placeholder="t('jitsi', 'Name of the new room')"
					maxlength="100"
					type="text">
				<button
					type="submit"
					class="empty-room-list__new__button icon-add" />
			</div>
		</form>
	</div>
</template>

<script>

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'EmptyRoomListItem',
	data() {
		return {
			name: '',
		}
	},
	mounted() {
		this.$refs.roomNameInput.focus()
	},
	methods: {
		async create() {
			const data = {
				name: this.name,
			}
			await axios.post(generateUrl('/apps/jitsi/rooms'), data)
			this.$emit('created')
		},
	},
}
</script>

<style scoped>

.empty-room-list {
	align-items: center;
	display: flex;
	flex-direction: column;
	padding: 64px 32px;
}

.empty-room-list__text {
	color: var(--color-text-lighter);
	font-size: 32px;
	margin-bottom: 32px;
	text-align: center;
}

.empty-room-list__new__label {
	margin-bottom: 8px;
}

.empty-room-list__new__input-container {
	display: flex;
}

.empty-room-list__new__input {
	flex: 1 0 auto;
	margin-right: 4px;
	width: 300px;
}

.empty-room-list__new__button {
	border-radius: var(--border-radius);
	padding-left: 16px;
	padding-right: 16px;
}

</style>
