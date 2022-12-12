<template>
	<div class="app-content">
		<Breadcrumbs>
			<Breadcrumb :disable-drop="true" title="Home" />
		</Breadcrumbs>
		<div>
			<div class="app-title">
				<h1 class="h1 app-title__text">
					{{ t('jitsi', 'Conference rooms') }}
				</h1>
				<button
					v-if="rooms.length > 0"
					class="icon-add app-title__button"
					@click="showCreateRoom = true" />
			</div>
			<CreateRoomItem
				v-if="showCreateRoom"
				@cancelled="showCreateRoom = false"
				@created="onRoomCreated" />
			<RoomList>
				<RoomListItem
					v-for="room in rooms"
					:key="room.id"
					:room="room"
					@deleted="refreshRooms" />
				<EmptyRoomListItem
					v-if="rooms.length === 0"
					@created="onRoomCreated" />
			</RoomList>
		</div>
	</div>
</template>

<script>

import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import EmptyRoomListItem from './components/EmptyRoomListItem'
import RoomList from './components/RoomList'
import RoomListItem from './components/RoomListItem'
import CreateRoomItem from './components/CreateRoomItem'
import Breadcrumb from '@nextcloud/vue/dist/Components/Breadcrumb'
import Breadcrumbs from '@nextcloud/vue/dist/Components/Breadcrumbs'

import 'vue-material-design-icons/styles.css'
import '../css/styles.css'

export default {
	name: 'Index',
	components: {
		Breadcrumb,
		Breadcrumbs,
		CreateRoomItem,
		EmptyRoomListItem,
		RoomList,
		RoomListItem,
	},
	data() {
		return {
			showCreateRoom: false,
			rooms: [],
		}
	},
	async created() {
		await this.refreshRooms()
	},
	methods: {
		async onRoomCreated() {
			this.showCreateRoom = false
			await this.refreshRooms()
		},
		async refreshRooms() {
			const response = await axios.get(generateUrl('/apps/jitsi/rooms'))
			this.rooms = response.data
		},
	},
}
</script>

<style scoped>

.app-title {
	align-items: center;
	display: flex;
	margin-bottom: 16px;
}

.app-title__text {
	margin-right: 8px;
	padding: 9px 0;
}

.app-title__button {
	padding: 16px;
}

.h1 {
	font-size: 24px;
}

.app-content {
	padding: 16px;
	width: 100%;
}

@media only screen and (min-width: 576px) {
	.app-content {
		margin-left: auto;
		margin-right: auto;
		max-width: 992px;
	}
}

</style>
