import Vue from 'vue'
import Index from './Index'
import AppGlobal from './mixins/AppGlobal'
import VueClipboard from 'vue-clipboard2'

Vue.use(VueClipboard)

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
		render: h => h(Index),
	})
})
