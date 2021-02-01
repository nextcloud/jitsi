import Vue from 'vue'
import Admin from './Admin'
import AppGlobal from './mixins/AppGlobal'

function ready(fn) {
	if (document.readyState !== 'loading') {
		fn()
	} else {
		document.addEventListener('DOMContentLoaded', fn)
	}
}

Vue.mixin(AppGlobal)

ready(() => {
	new Vue({
		el: '#jitsi',
		render: h => h(Admin),
	})
})
