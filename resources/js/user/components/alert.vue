<template>
    <modal
        :show="active"
        v-on:hide-modal="$emit('deActive')"
        :center="true"
        transition-type="modal-fade"
        :allow-to-close="true"
        :close-button="false"
        size="sm"
        :scroll="false"
        :container-style="{ width: '90%', height: 'auto', borderRadius: '5px', maxWidth: '375px' }"
        v-cloak-hidden
    >
        <div slot="body" class="text-center">
            <slot>
                <sweetalert-icon :icon="type"/>
                <div class="text-sm my-2" v-text="message"></div>
            </slot>
        </div>
    </modal>
</template>

<script>
	export default {
		props: ['type', 'active', 'message']
	}
</script>
