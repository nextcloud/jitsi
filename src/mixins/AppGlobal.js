import { generateUrl } from '@nextcloud/router'

export default {
	methods: {
		t,
		link(path) {
			return generateUrl(path)
		},
	},
}
