/* eslint-disable import/no-dynamic-require,global-require */

const klawSync = require('klaw-sync');
const path = require('path');
const slash = require('slash');

const rootPath = Mix.paths.rootPath;
const userPath = 'resources/sass/user';

/**
 * resources/sass/user/pages/all.scss
 * resources/sass/user/pages/web/main/all.scss
 * resources/sass/user/pages/store/main/all.scss
 *
 * resources/sass/user/pages/{anything}.scss
 * resources/sass/user/pages/{anything}/{anything}.scss
 * resources/sass/user/pages/{anything}/{anything}/{anything}.scss
 */
function handlePagesFolderRequire(mix, options) {
    const normalizedPath = path.join(rootPath, userPath, '/pages');

    klawSync(normalizedPath, {
        nodir: true,
        traverseAll: true,
        depthLimit: 2
    }).forEach(({ path: filePath }) => {
        const relativeToRootPath = slash(path.relative(rootPath, filePath));

        if(relativeToRootPath.split('/').pop() !== 'all.scss') return;

        mix.sass(relativeToRootPath, relativeToRootPath.replace(userPath, './css').replace('.scss', '.css'), options);
    });
}

module.exports = function (mix, options) {
    mix.sass('resources/sass/user/user.scss', './css/main/all.css',options);
    handlePagesFolderRequire(mix, options);
};

