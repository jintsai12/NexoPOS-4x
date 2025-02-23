<script>
import popupCloser from '@/libraries/popup-closer';
import nsPosCashRegistersActionPopupVue from './ns-pos-cash-registers-action-popup.vue';
import nsPosCashRegistersHistoryVue from './ns-pos-cash-registers-history-popup.vue';
import popupResolver from '@/libraries/popup-resolver';
import { __ } from '@/libraries/lang';
export default {
    mounted() {
        this.settingsSubscriber     =   POS.settings.subscribe( settings => {
            this.settings   =   settings;
        });

        this.popupCloser();

        this.loadRegisterSummary();
    },
    beforeDestroy() {
        this.settingsSubscriber.unsubscribe();
    },
    data() {
        return {
            settings: null,
            settingsSubscriber: null,
            register: {}
        }
    },
    methods: {
        __,
        
        popupResolver,

        popupCloser,

        loadRegisterSummary() {
            nsHttpClient.get( `/api/nexopos/v4/cash-registers/${this.settings.register.id}` )
                .subscribe( result => {
                    this.register   =   result;
                })
        },

        closePopup() {
            this.popupResolver({
                status: 'failed',
                button: 'close_popup'
            });
        },

        async closeCashRegister() {
            try {
                const response  =   await new Promise( ( resolve, reject ) => {
                    Popup.show( nsPosCashRegistersActionPopupVue, {
                        title: __( 'Close Register' ),
                        action: 'close',
                        identifier: 'ns.cash-registers-closing',
                        resolve, 
                        reject
                    })
                });

                /**
                 * if the register has been successfully
                 * closed, we need to delete the registe reference
                 */
                POS.unset( 'register' );
                
                this.popupResolver({
                    button: 'close_register',
                    ...response
                });
            } catch( exception ) {
                throw exception;
            }
        },

        async cashIn() {
            try {
                const response  =   await new Promise( ( resolve, reject ) => {
                    Popup.show( nsPosCashRegistersActionPopupVue, {
                        title: __( 'Cash In' ),
                        action: 'register-cash-in',
                        identifier: 'ns.cash-registers-cashing',
                        resolve, 
                        reject
                    })
                });

                /**
                 * if the register has been successfully
                 * closed, we need to delete the registe reference
                 */
                this.popupResolver({
                    button: 'close_register',
                    ...response
                });
            } catch( exception ) {
                throw exception;
            }
        },

        async cashOut() {
            try {
                const response  =   await new Promise( ( resolve, reject ) => {
                    Popup.show( nsPosCashRegistersActionPopupVue, {
                        title: __( 'Cash Out' ),
                        action: 'register-cash-out',
                        identifier: 'ns.cash-registers-cashout',
                        resolve, 
                        reject
                    })
                });

                /**
                 * if the register has been successfully
                 * closed, we need to delete the registe reference
                 */
                this.popupResolver({
                    button: 'close_register',
                    ...response
                });
            } catch( exception ) {
                throw exception;
            }
        },

        async historyPopup() {
            try {
                const response  =   await new Promise( ( resolve, reject ) => {
                    Popup.show( nsPosCashRegistersHistoryVue, { resolve, reject });
                });
            } catch( exception ) {
                throw exception;
            }
        }
    }
}
</script>
<template>
    <div class="shadow-lg w-95vw md:w-3/5-screen lg:w-2/4-screen bg-white">
        <div class="p-2 border-b border-gray-200 flex items-center justify-between">
            <h3>{{ __( 'Register Options' ) }}</h3>
            <div>
                <ns-close-button @click="closePopup()"></ns-close-button>
            </div>
        </div>
        <div v-if="register.total_sale_amount !== undefined && register.balance !== undefined">
            <div class="h-16 text-3xl bg-blue-400 text-white flex items-center justify-between px-3">
                <span class="">{{ __( 'Sales' ) }}</span>
                <span class="font-bold">{{ register.total_sale_amount | currency }}</span>
            </div>
            <div class="h-16 text-3xl bg-green-400 text-white flex items-center justify-between px-3">
                <span class="">{{ __( 'Balance' ) }}</span>
                <span class="font-bold">{{ register.balance | currency }}</span>
            </div>
        </div>
        <div class="h-32 border-gray-200 border-b py-1 flex items-center justify-center" v-if="register.total_sale_amount === undefined && register.balance === undefined">
            <div>
                <ns-spinner border="4" size="16"></ns-spinner>
            </div>
        </div>
        <div class="grid grid-cols-2 text-gray-700">
            <div @click="closeCashRegister()" class="hover:bg-teal-100 border-gray-200 border-r border-b py-4 cursor-pointer px-2 flex items-center justify-center flex-col">
                <i class="las la-sign-out-alt text-6xl"></i>
                <h3 class="text-xl font-bold">{{ __( 'Close' ) }}</h3>
            </div>
            <div @click="cashIn()" class="hover:bg-green-100 border-gray-200 border-r border-b py-4 cursor-pointer px-2 flex items-center justify-center flex-col">
                <i class="las la-plus-circle text-6xl"></i>
                <h3 class="text-xl font-bold">{{ __( 'Cash In' ) }}</h3>
            </div>
            <div @click="cashOut()" class="hover:bg-red-100 border-gray-200 border-r border-b py-4 cursor-pointer px-2 flex items-center justify-center flex-col">
                <i class="las la-minus-circle text-6xl"></i>
                <h3 class="text-xl font-bold">{{ __( 'Cash Out' ) }}</h3>
            </div>
            <div @click="historyPopup()" class="hover:bg-blue-100 border-gray-200 border-r border-b py-4 cursor-pointer px-2 flex items-center justify-center flex-col">
                <i class="las la-history text-6xl"></i>
                <h3 class="text-xl font-bold">{{ __( 'History' ) }}</h3>
            </div>
        </div>
    </div>
</template>