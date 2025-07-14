<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Presensi;
use Carbon\Carbon;

class FetchPresensiCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'presensi:fetch';

    /**
     * The console command description.
     */
    protected $description = 'Fetches attendance log data directly from the attendance machine via SOAP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Connecting to the attendance machine...');

        // --- Configuration for your attendance machine ---
        $deviceIp = '192.168.88.9'; // The IP of the machine itself
        $deviceKey = '0';            // The communication key (usually 0)

        // --- This is the SOAP XML request payload ---
        $soapRequest = "<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">{$deviceKey}</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";

        try {
            // 1. Send the SOAP request to the device's web service endpoint
            $response = Http::withBody($soapRequest, 'text/xml')
                ->timeout(30) // Set a 30-second timeout
                ->post("http://{$deviceIp}/iWsService");

            if (!$response->successful()) {
                $this->error('Failed to connect to the attendance machine. Please check the IP address.');
                return 1; // Exit with an error code
            }

            // 2. Parse the XML response to find all the <Row> elements
            preg_match_all('/<Row>(.*?)<\/Row>/s', $response->body(), $matches);

            if (empty($matches[1])) {
                $this->info('No new attendance logs were found on the machine.');
                return 0; // Exit successfully
            }

            $this->info(count($matches[1]) . ' logs found. Processing and storing...');

            // 3. Loop through each log entry and save it to our database
            foreach ($matches[1] as $rowXml) {
                $pin      = $this->parseTag($rowXml, 'PIN');
                $dateTime = $this->parseTag($rowXml, 'DateTime');
                $verified = (bool) $this->parseTag($rowXml, 'Verified');
                $status   = (int) $this->parseTag($rowXml, 'Status');

                // Skip if the essential data (PIN or DateTime) is missing
                if (!$pin || !$dateTime) {
                    continue;
                }

                // Use updateOrCreate to prevent creating duplicate log entries.
                // It finds a log with the same user_id and timestamp, or creates a new one.
                Presensi::updateOrCreate(
                    [
                        'user_id'   => $pin,
                        'timestamp' => Carbon::parse($dateTime),
                    ],
                    [
                        'verified' => $verified,
                        'status'   => $status,
                    ]
                );
            }

            $this->info('Successfully synchronized attendance data!');
            return 0; // Exit successfully

        } catch (\Exception $e) {
            // This will catch network connection errors or other issues
            $this->error('An error occurred: ' . $e->getMessage());
            return 1; // Exit with an error code
        }
    }

    /**
     * A helper function to parse values from inside XML tags.
     */
    private function parseTag(string $string, string $tag): ?string
    {
        if (preg_match("/<{$tag}>(.*?)<\/{$tag}>/", $string, $matches)) {
            return $matches[1];
        }
        return null;
    }
}