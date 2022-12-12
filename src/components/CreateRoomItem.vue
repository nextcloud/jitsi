<template>
	<div>
		<form @submit.prevent="create">
			<div class="create-room">
				<input
					ref="roomNameInput"
					v-model="name"
					class="create-room__name"
					:placeholder="t('jitsi', 'Name of the new room')"
					maxlength="100">
				<div class="create-room__actions">
					<Actions>
						<ActionButton icon="icon-checkmark" />
					</Actions>
					<Actions>
						<ActionButton
							icon="icon-close"
							@click.prevent="cancel" />
					</Actions>
				</div>
			</div>
			<button
				type="submit"
				style="display: none;" />
		</form>
	</div>
</template>

<script>

import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Actions from '@nextcloud/vue/dist/Components/Actions'

export default {
	name: 'CreateRoomItem',
	components: {
		ActionButton,
		Actions,
	},
	data() {
		return {
			name: '',
		}
	},
	mounted() {
		this.$refs.roomNameInput.focus()
	},
	methods: {
		cancel() {
			this.$emit('cancelled')
		},
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

.create-room {
	align-items: center;
	border: 1px solid var(--color-border);
	display: flex;
	margin-bottom: 16px;
	padding: 10px 16px;
}

.create-room__name {
	border: 0;
	flex: 1 0 auto;
	font-size: 14px;
	padding-left: 48px;
}

</style>
