<template>
	<div class="jitsi-info-container">
		<h1 class="jitsi-info-title">
			{{ t('jitsi', 'Room not found') }}
		</h1>
		<p class="jitsi-info-text">
			{{ t('jitsi', 'Please check the link/URL and ask your host') }}
		</p>
	</div>
</template>

<script>
export default {
	name: 'RoomNotFound',
}
</script>

<style scoped>

</style>
