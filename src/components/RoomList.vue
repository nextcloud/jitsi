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
    flex: 0 1 auto;
    overflow-y: scroll;
}

</style>
