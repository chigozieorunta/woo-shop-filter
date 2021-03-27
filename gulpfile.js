const { src, dest, watch } = require('gulp');

const minfiy = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const sass = require('gulp-sass')(require('sass'));

const compile = () => {
	return src()
		.pipe(sass())
		.pipe(autoprefixer())
		.pipe(minify())
		.pipe(dest());
}
