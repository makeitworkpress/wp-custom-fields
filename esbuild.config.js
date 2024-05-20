const esbuild = require('esbuild');

const config = {
    entryPoints: {
        'wpcf': './src/assets/js/app.ts',
    },
    outdir: './public/js/',
    bundle: true,
    minify: false,
    format: 'iife',
    platform: 'browser',
    target: 'es2022',
    loader: {
      '.ts': 'ts',
    },
    plugins: [
        {
            name: 'rebuild-notify',
            setup(build) {
                build.onEnd(result => {
                    console.log(`build ended with ${result.errors.length} errors`);
                })
            },
        },
    ],
};

(async function() {
    const ctx = await esbuild.context(config);
    await ctx.watch();
})();