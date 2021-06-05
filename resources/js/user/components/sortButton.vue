<template>
    <a  @click.prevent.stop="handleSort()" :class="{ 'cursor-pointer': !disabled }">
        <slot name="before"/>
        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="14" viewBox="0 0 8 14" class="text-gray-300 fill-current" :class="svgClass" v-if="!disabled">
            <g id="sortable-icon" fill="none" fill-rule="evenodd">
                <path id="Path-2-Copy-3"
                      d="M1.70710678 4.70710678c-.39052429.39052429-1.02368927.39052429-1.41421356 0-.3905243-.39052429-.3905243-1.02368927 0-1.41421356l3-3c.39052429-.3905243 1.02368927-.3905243 1.41421356 0l3 3c.39052429.39052429.39052429 1.02368927 0 1.41421356-.39052429.39052429-1.02368927.39052429-1.41421356 0L4 2.41421356 1.70710678 4.70710678z"
                      class="fill-current" :class="[active && sortType === 'asc' ? 'text-gray-500' : 'text-gray-400' ]"/>
                <path id="Combined-Shape-Copy-3" fill-rule="nonzero"
                      d="M6.29289322 9.29289322c.39052429-.39052429 1.02368927-.39052429 1.41421356 0 .39052429.39052429.39052429 1.02368928 0 1.41421358l-3 3c-.39052429.3905243-1.02368927.3905243-1.41421356 0l-3-3c-.3905243-.3905243-.3905243-1.02368929 0-1.41421358.3905243-.39052429 1.02368927-.39052429 1.41421356 0L4 11.5857864l2.29289322-2.29289318z"
                      class="fill-current"
                      :class="[active && sortType === 'desc' ? 'text-gray-500' : 'text-gray-400' ]"/>
            </g>
        </svg>
        <slot name="after"/>
    </a>
</template>

<script>

    const DESC = 'desc';
    const ASC = 'asc';

	export default {
		props: {
            active: {},
            type: {},
            svgClass: { default: []},
            loading: { default: false },
            disabled: { default: false }
        },

        data() {
		    return {
		        sortType: this.type || ''
            }
        },

        watch:{
		    active(value) {
		        if(!value)
		            this.sortType = '';
            }
        },

        methods: {
            handleSort() {
                if(this.loading || this.disabled) return;
                this.sortType = this.sortType === ASC ? DESC : ASC;
                this.$emit('sort', this.sortType);
            }
        }
	}
</script>

<style scoped>

</style>
