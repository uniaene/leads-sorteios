module.exports = {
    apps: [
        {
            name: "sorteio-api",
            script: "src/server.js",
            instances: "max",
            exec_mode: "cluster",
            env: { NODE_ENV: "production" }
        },
        {
            name: "sorteio-worker",
            script: "src/worker.js",
            instances: 1,
            env: { NODE_ENV: "production" }
        }
    ]
};
