const { src, dest, watch } = require('gulp');

const minify = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const sass = require('gulp-sass')(require('sass'));

const paths = {
	src: {
		css: './assets/css/src/**/*.scss'
	},
	dist: {
		css: './assets/css/dist'
	}
}

const compile = () => {
	return src(paths.src.css)
		.pipe(sass())
		.pipe(autoprefixer())
		//.pipe(minify())
		.pipe(dest(paths.dist.css));
}

const observe = () => {
	watch(paths.src.css, compile);
}

exports.sass = compile;
exports.watch = observe;
