const path = require('path');
const NODE_ENV = process.env.NODE_ENV || "development";

module.exports = {
    watch: true,
    mode: NODE_ENV,
    entry: './src/{PROJECT_NAME}.es6',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: './{PROJECT_NAME}.js',
        libraryTarget: "umd"
    },

    module: {
        rules: [
            {
                test: /\.es6$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [ 'es2015', 'react', 'stage-2' ]
                    }
                }
            }
        ]
    }
};