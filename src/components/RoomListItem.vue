<template>
	<div class="room-list-item">
		<Avatar
			class="room-list-item__icon"
			:display-name="filteredName"
			:disable-tooltip="true" />
		<div class="room-list-item__name">
			{{ room.name }}
		</div>
		<div class="room-list-item__actions">
			<Actions>
				<ActionLink
					:href="roomUrl"
					icon="icon-play">
					{{ t('jitsi', 'Join') }}
				</ActionLink>
			</Actions>
			<Actions ref="copyLinkActions">
				<ActionLink
					:href="roomUrl"
					:icon="copied && copySuccess ? 'icon-checkmark-color' : 'icon-clippy'"
					@click.stop.prevent="copyLink">
					{{ clipboardTooltip }}
				</ActionLink>
			</Actions>
			<Actions
				:force-menu="true">
				<ActionButton
					icon="icon-delete"
					@click="deleteRoom">
					{{ t('jitsi', 'Delete room') }}
				</ActionButton>
			</Actions>
		</div>
	</div>
</template>

<script>

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'

export default {
	name: 'RoomListItem',
	components: {
		ActionButton,
		ActionLink,
		Actions,
		Avatar,
	},
	props: {
		room: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			copied: false,
			copySuccess: false,
		}
	},
	computed: {
		filteredName() {
			return this.room.name
				.replace('.', '_')
				.replace(/[^a-zA-Z0-9_-]+/g, '')
		},
		roomUrl() {
			return window.location.protocol + '//'
				+ window.location.host
				+ generateUrl(`/apps/jitsi/rooms/${this.room.publicId}/${this.filteredName}`)
		},
		clipboardTooltip() {
			if (this.copied) {
				return this.copySuccess
					? t('jjitsi', this.t('Link copied'))
					: t('jjitsi', this.t('Cannot copy, please copy the link manually'))
			}
			return t('jjitsi', this.t('Copy to clipboard'))
		},
	},
	methods: {
		async deleteRoom() {
			await axios.delete(generateUrl('/apps/jitsi/rooms/' + this.room.id))
			this.$emit('deleted')
		},
		async copyLink() {
			try {
				await this.$copyText(this.roomUrl)
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
	},
}

</script>

<style scoped>

.room-list-item {
	align-items: center;
	border-bottom: 1px solid var(--color-border);
	display: flex;
	padding: 10px 16px;
}

.room-list-item:last-of-type {
	border-bottom: 0;
}

.room-list-item__icon {
	margin-right: 16px;
}

.room-list-item__name {
	flex: 1 0 auto;
	margin-right: 16px;
}

.room-list-item__actions {
	align-items: center;
	display: flex;
}

</style>
