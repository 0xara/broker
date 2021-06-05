class Svg {
	constructor(svg) {
		let div = document.createElement('div');
		div.innerHTML = svg; // be careful with dynamic webpack requires.

		let fragment = document.createDocumentFragment();
		fragment.appendChild(div);

		this.svg = fragment.querySelector('svg');
	}

	classes(classes) {
		if (classes) {
			classes.split(' ').forEach(className => {
				this.svg.classList.add(className);
			});
		}

		return this;
	}

	width(width) {
		if (width) {
			this.svg.setAttribute('width', width);
		}

		return this;
	}

	height(height) {
		if (height) {
			this.svg.setAttribute('height', height);
		}

		return this;
	}

	getElement() {
		return this.svg;
	}

	toString() {
		return this.svg.outerHTML || new XMLSerializer().serializeToString(this.svg);
	}
}
// https://stackoverflow.com/questions/56452235/use-dynamic-vue-components-in-render-function
//https://medium.com/scrumpy/dynamic-component-templates-with-vue-js-d9236ab183bb
// we can pass object to render function h({ template: new Svg(svg).... })
//https://github.com/webpack/webpack/issues/4807

export default {
	props: ['name', 'classes', 'width', 'height'],

	template: '<component :is="component" v-if="component"/>',

	data() {
		return {
			svg: ''
		}
	},

	computed: {
		component() {
			if(this.svg) {
				return {
					template: this.svg
				}
			}

			return '';
		}
	},

	created() {
		import(/* webpackChunkName: "js/svg/[request]" */`./../../assets/svg/${this.name}`).then((svg) => {
			this.svg = new Svg(svg)
                .classes(this.classes)
                .width(this.width)
                .height(this.height)
                .toString();
		}).catch(() => {})
	}
};
