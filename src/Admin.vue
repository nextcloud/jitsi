<template>
    <div>
        <form @submit.prevent="submit">
            <fieldset :disabled="saving">
                <SettingsSection title="Jitsi">
                    <div v-if="loading">
                        {{ t('jitsi', 'loading…') }}
                    </div>
                    <div v-if="!loading">
                        <div class="group">
                            <label
                                for="jitsi_server_url"
                                class="label">
                                {{ t('jitsi', 'Server URL (required)') }}
                            </label>
                            <div class="input-group">
                                <input
                                    id="jitsi_server_url"
                                    v-model="serverUrl"
                                    class="input"
                                    type="text">
                                <div v-if="serverUrlStatus" :class="`${serverUrlStatus}-text`">
                                    {{ serverUrlMessage }}
                                </div>
                            </div>
                        </div>
                        <div class="group">
                            <label
                                for="jitsi_jwt_secret"
                                class="label">
                                {{ t('jitsi', 'JWT Secret (optional)') }}
                            </label>
                            <div class="input-group">
                                <input
                                    id="jitsi_jwt_secret"
                                    v-model="jwtSecret"
                                    class="input"
                                    type="text">
                            </div>
                        </div>
                        <div class="group">
                            <label
                                for="jitsi_help_link"
                                class="label">
                                {{ t('jitsi', 'Help link (optional)') }}
                            </label>
                            <div class="input-group">
                                <input
                                    id="jitsi_help_link"
                                    v-model="helpLink"
                                    class="input"
                                    type="text">
                            </div>
                        </div>
                        <div class="group">
                            <label
                                for="display_join_using_the_jitsi_app"
                                class="label">
                                {{ t('jitsi', 'Display "Join using the Jitsi app"') }}
                            </label>
                            <div class="input-group">
                                <input
                                    id="display_join_using_the_jitsi_app"
                                    class="admin-checkbox"
                                    v-model="displayJoinUsingTheJitsiApp"
                                    type="checkbox">
                            </div>
                        </div>
                        <div class="group group--centered">
                            <button
                                type="submit"
                                class="primary"
                                :disabled="saving">
                                {{ t('jitsi', 'save') }}
                            </button>
                            <span
                                v-if="!saving && saved"
                                class="msg success">
								{{ t('jitsi', 'saved') }}
							</span>
                            <span
                                v-if="saving"
                                class="msg">
								{{ t('jitsi', 'saving…') }}
							</span>
                        </div>
                    </div>
                </SettingsSection>
            </fieldset>
        </form>
    </div>
</template>

<script>

import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'

export default {
    name: 'Admin',
    components: {
        SettingsSection,
    },
    data () {
        return {
            loading: true,
            saving: false,
            saved: false,
            errorMessage: '',
            jwtSecret: '',
            serverUrl: '',
            serverUrlStatus: false,
            serverUrlMessage: '',
            helpLink: '',
            rawDisplayJoinUsingTheJitsiApp: 0
        }
    },
    async created () {
        this.jwtSecret = await this.loadSetting('jwt_secret')
        this.serverUrl = await this.loadSetting('jitsi_server_url')
        this.helpLink = await this.loadSetting('help_link')

        const rawDisplayJoinUsingTheJitsiApp = await this.loadSetting('display_join_using_the_jitsi_app', '1')
        this.rawDisplayJoinUsingTheJitsiApp = parseInt(rawDisplayJoinUsingTheJitsiApp, 10)

        this.loading = false
    },
    methods: {
        async submit () {
            this.sanitise()
            this.validate()

            if (this.hasError) {
                return
            }

            this.saving = true
            this.saved = false

            await Promise.all([
                await this.updateSetting('jitsi_server_url', this.serverUrl),
                await this.updateSetting('jwt_secret', this.jwtSecret),
                await this.updateSetting('help_link', this.helpLink),
                await this.updateSetting('display_join_using_the_jitsi_app', this.rawDisplayJoinUsingTheJitsiApp),
            ])

            this.saving = false
            this.saved = true
        },
        sanitise() {
            if (this.serverUrl && !this.serverUrl.endsWith('/')) {
                this.serverUrl += '/'
            }
        },
        validate() {
            this.serverUrlStatus = false
            this.serverUrlMessage = ''

            if (!this.serverUrl) {
                this.serverUrlStatus = 'error'
                this.serverUrlMessage = this.t('jitsi', 'Please provide a Jitsi instance URL')
            }

            if (!this.serverUrl.startsWith('https://')) {
                this.serverUrlStatus = 'error'
                this.serverUrlMessage = this.t('jitsi', 'The server URL must start with https://')
            }

            if (this.serverUrl === 'https://meet.jit.si/') {
                this.serverUrlStatus = 'warning'
                this.serverUrlMessage = this.t('jitsi', 'It is highly recommended to set up a dedicated Jitsi instance')
            }
        },
        async updateSetting (name, value) {
            try {
                await new Promise((resolve, reject) =>
                    OCP.AppConfig.setValue('jitsi', name, value, {
                        success: resolve,
                        error: reject,
                    })
                )
            } catch (e) {
                this.error = this.t('jitsi', 'Failed to save settings')
                throw e
            }
        },
        async loadSetting (name, defaultValue = null) {
            try {
                const resDocument = await new Promise((resolve, reject) =>
                    OCP.AppConfig.getValue('jitsi', name, defaultValue, {
                        success: resolve,
                        error: reject,
                    })
                )
                if (resDocument.querySelector('status').textContent !== 'ok') {
                    this.errorMessage = this.t('jitsi', 'Failed to load settings')
                    console.error('Failed request', resDocument)
                    return
                }
                const dataEl = resDocument.querySelector('data')
                return dataEl.firstElementChild.textContent
            } catch (e) {
                this.errorMessage = this.t('jitsi', 'Failed to load settings')
                throw e
            }
        },
    },
    computed: {
        displayJoinUsingTheJitsiApp: {
            get () {
                return this.rawDisplayJoinUsingTheJitsiApp === '1'
            },
            set (value) {
                this.rawDisplayJoinUsingTheJitsiApp = value ? '1' : '0'
            }
        },
        hasError() {
            return this.serverUrlStatus === 'error'
        }
    }
}
</script>

<style scoped>
.group {
    align-items: flex-start;
    display: flex;
}

.group--centered {
    align-items: center;
}

.label {
    display: block;
    width: 100%;
}

.input {
    display: block;
    width: 100%;
}

.input-group {
    margin-bottom: 8px;
    position: relative;
    top: -7px;
    width: 100%;
}

.input {
    margin-bottom: 0;
}

.input--has-warning {
    border-color: var(--color-warning);
}

.input--has-error {
    border-color: var(--color-error);
}

.warning-text {
    color: var(--color-warning);
    font-size: .9em;
}

.error-text {
    color: var(--color-error);
    font-size: .9em;
}

.admin-checkbox {
    cursor: pointer;
}

@media only screen and (min-width: 576px) {
    .label {
        display: inline-block;
        margin-right: 10px;
        width: 200px;
    }

    .input-group {
        display: inline-block;
        width: 400px;
    }

    button.primary {
        margin-left: 210px;
    }
}

</style>
