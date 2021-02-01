<template>
	<div class="room-list">
		<slot />
	</div>
</template>

<script>
export default {
	name: 'RoomList',
}
</script>

<style scoped>

.room-list {
	border: 1px solid var(--color-border);
}

</style>
