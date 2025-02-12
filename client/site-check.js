

const PASSPHRASE = 'ThisIsMyBoomstick!';
const SERVER_URL = 'http://localhost';
const CLIENT_NAME = 'John Snow(JS)';
const DO_VERBOSE = true;

const log = (data) => {
    if (!DO_VERBOSE) {
        return;
    }

    console.log(data);
}

const pause = async (milliseconds) => {
    return new Promise((success) => {
        setTimeout(() => {
            success();
        }, milliseconds)
    })
};

const main = async () => {
    log('Run Site Availability Checker');

    const getEndpointListUrl = new URL(SERVER_URL + '/api/metrics/source_list');
    getEndpointListUrl.searchParams.set('auth', PASSPHRASE);

    let fetchEndpointListResponse;

    try {
        fetchEndpointListResponse = await fetch(getEndpointListUrl.href);

        if (!fetchEndpointListResponse.ok) {
            throw new Error("Can't reach the server. Wrapping up.")
        }
    } catch (error) {
        log("Can't reach the server. Wrapping up.");
        return;
    }

    const endpointList = await fetchEndpointListResponse.json();
    log("Got the list of endpoints");

    const results = [];

    // Testing the sites/endpoints
    await Promise.all(endpointList.map(async (record) => {
        const urlToCheck = record.endpoint;

        const tryAmount = 3;
        const tries = [];
        for (let tryIdx = 0; tryIdx < tryAmount; tryIdx++ ) {
            let timingMilliseconds = 999999999; // -1 means the site is not reachable
            const tsStart = Date.now();

            try {
                const result = await fetch(urlToCheck);
                if (result.ok) {
                    timingMilliseconds = Date.now() - tsStart;
                }
            } catch (error) {

            }
            log(urlToCheck + ' - ' + timingMilliseconds + 'ms');
            tries.push(timingMilliseconds);
            await pause(1000);
        }

        const finalScore = Math.round(
            tries.reduce((previousValue, currentValue) => previousValue + currentValue, 0) / tries.length
        );

        log(urlToCheck + ' - FINAL: ' + finalScore + 'ms');

        results.push({
            endpoint: urlToCheck,
            timingMilliseconds: finalScore,
        });
    }));

    log(results);
    try {
        const saveResultsResponse = await fetch(
            SERVER_URL + '/api/metrics',
            {
                method: 'POST',
                body: JSON.stringify({
                   client: CLIENT_NAME,
                   results,
                   auth: PASSPHRASE,
                }),
                headers:{"Content-type": "application/json; charset=UTF-8"}
            },
        );

        if (!saveResultsResponse.ok) {
            throw new Error("WARNING! Couldn't save the results. Exiting....");
        }
    } catch (error) {
        log("WARNING! Couldn't save the results. Exiting....");
    }



    log('Done');
};


main();
