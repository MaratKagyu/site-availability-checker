#!/bin/bash

# Configuration
PASSPHRASE="ThisIsMyBoomstick!"
SERVER_URL="http://localhost"
CLIENT_NAME="John Snow(Bash)"
DO_VERBOSE=true
TRY_AMOUNT=3
PAUSE_DURATION=1  # Pause duration in seconds

# Function to log messages conditionally
log() {
    if [ "$DO_VERBOSE" = true ]; then
        echo "$1"
    fi
}

# Function to pause for a given number of milliseconds
pause() {
    sleep "$1"
}

get_current_milliseconds() {
  if ! date --version >/dev/null 2>&1; then # Mac
      local microseconds
      microseconds=$(perl -e 'use Time::HiRes qw( gettimeofday ); my ($a, $b) = gettimeofday; print $b;')
      local milliseconds
      milliseconds=$((microseconds/1000))
      echo $(($(date +%s)*1000 + milliseconds))
  else
      echo "(date +%s%3N)"
  fi
}

# Main function
main() {
    log "Run Site Availability Checker"

    # Construct the URL to get the list of endpoints
    ENDPOINT_LIST_URL="${SERVER_URL}/api/metrics/source_list?auth=${PASSPHRASE}"

    # Fetching the list of endpoints
    endpoint_list=$(curl -s "$ENDPOINT_LIST_URL")

    if [ "$endpoint_list" = "" ]; then
        log "Can't reach the server. Wrapping up."
        return
    fi

    # Parse the JSON array of endpoints using jq
    endpoints=$(echo "$endpoint_list" | jq -r '.[] | .endpoint')

    log "Got the list of endpoints"

    # Initialize an array for results
    results=()

    # Loop through each endpoint and check availability
    for urlToCheck in $endpoints; do
        log "Checking $urlToCheck"
        tries=()

        for ((tryIdx=0; tryIdx<TRY_AMOUNT; tryIdx++)); do
            start_time=$(get_current_milliseconds)   # Start time in milliseconds
            timingMilliseconds=999999999

            # Test the endpoint
            response=$(curl -s -o /dev/null -w "%{http_code}" "$urlToCheck")
            if [ "$response" -ge 200 ] && [ "$response" -le 399 ]; then
                end_time=$(get_current_milliseconds)
                timingMilliseconds=$((end_time - start_time))
            fi

            log "$urlToCheck - ${timingMilliseconds}ms"
            tries+=("$timingMilliseconds")

            pause "$PAUSE_DURATION"
        done

        # Calculate the average response time
        sum=0
        for time in "${tries[@]}"; do
            sum=$((sum + time))
        done
        finalScore=$((sum / ${#tries[@]}))

        log "$urlToCheck - FINAL: ${finalScore}ms"

        # Add result to array
        results+=("{\"endpoint\": \"$urlToCheck\", \"timingMilliseconds\": $finalScore}")
    done

    # Convert results array to JSON
    results_json=$(printf "%s\n" "${results[@]}" | jq -s '.')

    # Post the results to the server
    post_data=$(jq -n \
      --arg client "$CLIENT_NAME" \
      --argjson results "$results_json" \
      --arg auth "$PASSPHRASE" \
      '{client: $client, results: $results, auth: $auth}')

    saveResultsResponse=$(curl -s -o /dev/null -w "%{http_code}" \
        -X POST "${SERVER_URL}/api/metrics" \
        -H "Content-Type: application/json" \
        -d "$post_data")

    if [ "$saveResultsResponse" -ne 200 ]; then
        log "WARNING! Couldn't save the results. Exiting...."
    fi

    log "Done"
}

main
