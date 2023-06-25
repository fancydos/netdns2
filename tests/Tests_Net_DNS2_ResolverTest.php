<?php

/**
 * DNS Library for handling lookups and updates.
 *
 * Copyright (c) 2020, Mike Pultz <mike@mikepultz.com>. All rights reserved.
 *
 * See LICENSE for more details.
 *
 * @category  Networking
 * @package   Net_DNS2
 * @author    Mike Pultz <mike@mikepultz.com>
 * @copyright 2020 Mike Pultz <mike@mikepultz.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      https://netdns2.com/
 * @since     File available since Release 1.0.0
 *
 */

require_once 'Net/DNS2.php';

/**
 * This test uses the Google public DNS servers to perform a resolution test;
 * this should work on *nix and Windows, but will require an internet connection.
 *
 */
class Tests_Net_DNS2_ResolverTest extends PHPUnit\Framework\TestCase
{
    /**
     * function to test the resolver
     *
     * @return void
     * @access public
     *
     */
    public function testResolver()
    {
        $ns = [ '8.8.8.8', '8.8.4.4' ];

        $r = new Net_DNS2_Resolver([ 'nameservers' => $ns ]);

        $result = $r->query('google.com', 'MX');

        $this->assertSame($result->header->qr, Net_DNS2_Lookups::QR_RESPONSE);
        $this->assertSame(count($result->question), 1);
        $this->assertTrue(count($result->answer) > 0);
        $this->assertTrue($result->answer[0] instanceof Net_DNS2_RR_MX);
    }

    /**
     * function to test the resolver against our internal bind instance
     *
     * @return void
     * @access public
     *
     */
    public function testInternalResolver()
    {
        $ns = [ '3.136.56.244' ];

        $rrs = [

            //
            // skip this since it's going to change everytime I update the serial
            //
            //'SOA'           => [
            //
            //    'name'  => 'netdns2.com',
            //    'res'   => 'netdns2.com. 86400 IN SOA ns.netdns2.com. abuse.netdns2.com. 2022113035 21600 3600 604800 86400'
            //],

            //
            // suported by bind
            //
            'A'             => 'a.netdns2.com. 86400 IN A 1.1.1.1',
            'AAAA'          => 'aaaa.netdns2.com. 86400 IN AAAA 2600:1f16:17c:3950:47ac:cb79:62ba:702e',
            'AFSDB'         => 'afsdb.netdns2.com. 86400 IN AFSDB 3 afsdb.example.com.',
            'AMTRELAY'      => 'amtrelay.netdns2.com. 86400 IN AMTRELAY 10 0 2 2600:1f16:17c:3950:47ac:cb79:62ba:702e',
            'APL'           => 'apl.netdns2.com. 86400 IN APL 1:224.0.0.0/4 2:ff:0:0:0:0:0:0:0/8',
            'ATMA'          => 'atma.netdns2.com. 86400 IN ATMA 39246f000e7c9c03120001000100001234567800',
            'AVC'           => 'avc.netdns2.com. 86400 IN AVC "first record" "another records" "a third"',
            'CAA'           => 'caa.netdns2.com. 86400 IN CAA 0 issue "ca.example.net; policy=ev"',
            'CDNSKEY'       => 'cdnskey.netdns2.com. 86400 IN CDNSKEY 256 3 7 AwEAAYCXh/ZABi8kiJIDXYmyUlHzC0CHeBzqcpyZAIjC7dK1wkRYVcUvIlpTOpnOVVfcC3Py9Ui/x45qKb0LytvK7WYAe3WyOOwk5klwIqRC/0p4luafbd2yhRMF7quOBVqYrLoHwv8i9LrV+r8dhB7rXv/lkTSI6mEZsg5rDfee8Yy1',
            'CDS'           => 'cds.netdns2.com. 86400 IN CDS 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
            'CERT'          => 'cert.netdns2.com. 86400 IN CERT 3 0 0 TUlJQ1hnSUJBQUtCZ1FDcXlqbzNFMTU0dFU1Um43ajlKTFZsOGIwcUlCSVpGWENFelZvanVJT1BsMTM0by9zcHkxSE1hQytiUGh3Wk1UYVd4QlJpZHBFbUprNlEwNFJNTXdqdkFyLzFKWjhnWThtTzdCdTh1RUROVkNWeG5rQkUzMHhDSjhHRTNzL3EyN2VWSXBCUGFtU1lkNDVKZjNIeVBRRE4yaU45RjVHdGlIa2E2OXNhcmtKUnJ3SURBUUFCQW9HQkFJaUtDQ1NEM2FFUEFjQUx1MjdWN0JmR1BYN3lDTVg0OSsyVDVwNXNJdkduQjcrQ0NZZ09QaVQybmlpMGJPNVBBOTlnZnhPQXl1WCs5Z3llclVQbUFSc1ViUzcvUndkNGorRUlOVW1DanJSK2R6dGVXT0syeGxHamFOdGNPZU5jMkVtelQyMFRsekxVeUxTWGpzMzVlU2NQK0loeVptM2xJd21vbWtNb2d1QkFrRUE0a1FsOVBxaTJ2MVBDeGJCelU4Nnphblo2b0hsV0IzMUh4MllCNmFLYXhjNkVOZHhVejFzNjU2VncrRDhSVGpoSllyeDdMVkxzZDBRaVZJM0liSjVvUUpCQU1FN3k0aHg0SCtnQU40MEdrYjNjTFZGNHNpSEZrNnA2QVZRdlpzREwvVnh3bVlOdE4rM0txT3NVcG11WXZ3a3h0ajhIQnZtckxUYStXb3NmRDQwS1U4Q1FRQ1dvNmhob1R3cmI5bmdHQmFQQ2VDc2JCaVkrRUlvbUVsSm5mcEpuYWNxQlJ5emVid0pIeXdVOGsvalNUYXJIMk5HQzJ0bG5JMzRyS1VGeDZiTTJIWUJBa0VBbXBYSWZPNkZKL1NMM1RlWGNnQ1A5U1RraVlHd2NkdnhGeGVCcDlvRDZ2cElCN2FkWlgrMko5dzY5R0VUSlI0U3loSGVOdC95ZUhqWm9YdlhKVGc3ZHdKQVpEamxwL25wNEFZV3JYaGFrMVAvNGZlaDVNSU5WVHNXQkhTNlRZNW0xRmZMUEpybklHNW1FSHNidWkvdnhuQ1JmRUR4ZlU1V1E0cS9HUkZuaVl3SHB3PT0=',
            'CNAME'         => 'cname.netdns2.com. 86400 IN CNAME www.example.com.',
            'CSYNC'         => 'csync.netdns2.com. 86400 IN CSYNC 1278700841 3 A NS AAAA',
            'DHCID'         => 'dhcid.netdns2.com. 86400 IN DHCID AAIBY2/AuCccgoJbsaxcQc9TUapptP69lOjxfNuVAA2kjEA=',
            'DLV'           => 'dlv.netdns2.com. 86400 IN DLV 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
            'DNAME'         => 'dname.netdns2.com. 86400 IN DNAME frobozz-division.acme.example.',
            'DNSKEY'        => 'dnskey.netdns2.com. 86400 IN DNSKEY 256 3 7 AwEAAYCXh/ZABi8kiJIDXYmyUlHzC0CHeBzqcpyZAIjC7dK1wkRYVcUvIlpTOpnOVVfcC3Py9Ui/x45qKb0LytvK7WYAe3WyOOwk5klwIqRC/0p4luafbd2yhRMF7quOBVqYrLoHwv8i9LrV+r8dhB7rXv/lkTSI6mEZsg5rDfee8Yy1',
            'DS'            => 'ds.netdns2.com. 86400 IN DS 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
            'EUI48'         => 'eui48.netdns2.com. 86400 IN EUI48 00-00-5e-00-53-2a',
            'EUI64'         => 'eui64.netdns2.com. 86400 IN EUI64 00-00-5e-ef-10-00-00-2a',
            'HINFO'         => 'hinfo.netdns2.com. 86400 IN HINFO "PC-Intel-700mhz" "Redhat \"Linux\" 7.1"',
            'HIP'           => 'hip.netdns2.com. 86400 IN HIP 2 200100107B1A74DF365639CC39F1D578 AwEAAbdxyhNuSutc5EMzxTs9LBPCIkOFH8cIvM4p9+LrV4e19WzK00+CI6zBCQTdtWsuxKbWIy87UOoJTwkUs7lBu+Upr1gsNrut79ryra+bSRGQb1slImA8YVJyuIDsj7kwzG7jnERNqnWxZ48AWkskmdHaVDP4BcelrTI3rMXdXF5D rvs.example.com. another.example.com. test.domain.org.',
            'IPSECKEY'      => 'ipseckey.netdns2.com. 86400 IN IPSECKEY 10 2 2 2001:db8:0:8002:0:0:2000:1 AQNRU3mG7TVTO2BkR47usntb102uFJtugbo6BSGvgqt4AQ==',
            'ISDN'          => 'isdn.netdns2.com. 86400 IN ISDN "150 862 028 003 217" "42"',
            'KEY'           => 'key.netdns2.com. 86400 IN KEY 256 3 7 AwEAAYCXh/ZABi8kiJIDXYmyUlHzC0CHeBzqcpyZAIjC7dK1wkRYVcUvIlpTOpnOVVfcC3Py9Ui/x45qKb0LytvK7WYAe3WyOOwk5klwIqRC/0p4luafbd2yhRMF7quOBVqYrLoHwv8i9LrV+r8dhB7rXv/lkTSI6mEZsg5rDfee8Yy1',
            'KX'            => 'kx.netdns2.com. 86400 IN KX 10 mx1.mrhost.ca.',
            'L32'           => 'l32.netdns2.com. 86400 IN L32 10 10.1.2.0',
            'L64'           => 'l64.netdns2.com. 86400 IN L64 10 2001:db8:1140:1000',
            'LOC'           => 'loc.netdns2.com. 86400 IN LOC 42 21 54.675 N 71 06 18.343 W 24.12m 30.00m 40.00m 5.00m',
            'LP'            => 'lp.netdns2.com. 86400 IN LP 10 l64-subnet1.example.com.',
            'MX'            => 'mx.netdns2.com. 86400 IN MX 10 mx.netdns2.com.',
            'NAPTR'         => 'naptr.netdns2.com. 86400 IN NAPTR 100 10 "S" "SIP+D2U" "!^.*$!sip:customer-service@example.com!" _sip._udp.example.com.',
            'NID'           => 'nid.netdns2.com. 86400 IN NID 10 14:4fff:ff20:ee64',
            'NSAP'          => 'nsap.netdns2.com. 86400 IN NSAP 0x47.0005.80.005a00.0000.0001.e133.aaaaaa000151.00',
            'NSEC3PARAM'    => 'nsec3param.netdns2.com. 86400 IN NSEC3PARAM 1 0 1 D399EAAB',
            'NSEC'          => 'nsec.netdns2.com. 86400 IN NSEC test.host.com. A MX RRSIG NSEC TYPE1234',
            'NS'            => [

                'name'  => 'netdns2.com',
                'res'   => 'netdns2.com. 86400 IN NS ns.netdns2.com.'
            ],
            'OPENPGPKEY'    => [

                'name'  => '8d5730bd8d76d417bf974c03f59eedb7af98cb5c3dc73ea8ebbd54b7._openpgpkey.netdns2.com',
                'res'   => '8d5730bd8d76d417bf974c03f59eedb7af98cb5c3dc73ea8ebbd54b7._openpgpkey.netdns2.com. 86400 IN OPENPGPKEY d0VBQVlDWGgvWkFCaThraUpJRFhZbXlVbEh6QzBDSGVCenFjcHlaQUlqQzdkSzF3a1JZVmNVdklscFRPcG5PVlZmY0MzUHk5VWkveDQ1cUtiMEx5dHZLN1dZQWUzV3lPT3drNWtsd0lxUkMvMHA0bHVhZmJkMnloUk1GN3F1T0JWcVlyTG9Id3Y4aTlMclYrcjhkaEI3clh2L2xrVFNJNm1FWnNnNXJEZmVlOFl5MQ=='
            ],
            'PX'            => 'px.netdns2.com. 86400 IN PX 10 ab.net2.it. o-ab.prmd-net2.admdb.c-it.',
            'RP'            => 'rp.netdns2.com. 86400 IN RP louie\.trantor.umd.edu. lam1.people.test.com.',
            'RRSIG'         => 'rrsig.netdns2.com. 86400 IN RRSIG DNSKEY 7 1 86400 20100827211706 20100822211706 57970 gov. KoWPhMtLHp8sWYZSgsMiYJKB9P71CQmh9CnxJCs5GutKfo7Jpw+nNnDLiNnsd6U1JSkf99rYRWCyOTAPC47xkHr+2Uh7n6HDJznfdCzRa/v9uwEcbXIxCZ7KfzNJewW3EvYAxDIrW6sY/4MAsjS5XM/O9LaWzw6pf7TX5obBbLI+zRECbPNTdY+RF6Fl9K0GVaEZJNYi2PRXnATwvwca2CNRWxeMT/dF5STUram3cWjH0Pkm19Gc1jbdzlZVDbUudDauWoHcc0mfH7PV1sMpe80NqK7yQ24AzAkXSiknO13itHsCe4LECUu0/OtnhHg2swwXaVTf5hqHYpzi3bQenw==',
            'RT'            => 'rt.netdns2.com. 86400 IN RT 2 relay.prime.com.',
            'SIG'           => 'sig.netdns2.com. 86400 IN SIG DNSKEY 7 1 86400 20100827211706 20100822211706 57970 gov. KoWPhMtLHp8sWYZSgsMiYJKB9P71CQmh9CnxJCs5GutKfo7Jpw+nNnDLiNnsd6U1JSkf99rYRWCyOTAPC47xkHr+2Uh7n6HDJznfdCzRa/v9uwEcbXIxCZ7KfzNJewW3EvYAxDIrW6sY/4MAsjS5XM/O9LaWzw6pf7TX5obBbLI+zRECbPNTdY+RF6Fl9K0GVaEZJNYi2PRXnATwvwca2CNRWxeMT/dF5STUram3cWjH0Pkm19Gc1jbdzlZVDbUudDauWoHcc0mfH7PV1sMpe80NqK7yQ24AzAkXSiknO13itHsCe4LECUu0/OtnhHg2swwXaVTf5hqHYpzi3bQenw==',
            'SMIMEA'        => [

                'name'  => 'c93f1e400f26708f98cb19d936620da35eec8f72e57f9eec01c1afd6._smimecert.netdns2.com',
                'res'   => 'c93f1e400f26708f98cb19d936620da35eec8f72e57f9eec01c1afd6._smimecert.netdns2.com. 86400 IN SMIMEA 1 1 2 92003ba34942dc74152e2f2c408d29eca5a520e7f2e06bb944f4dca346baf63c1b177615d466f6c4b71c216a50292bd58c9ebdd2f74e38fe51ffd48c43326cbc'
            ],
            'SPF'           => 'spf.netdns2.com. 86400 IN SPF "v=spf1 ip4:192.168.0.1/24 mx ?all"',
            'SRV'           => 'srv.netdns2.com. 86400 IN SRV 20 0 5269 xmpp-server2.l.google.com.',
            'SSHFP'         => 'sshfp.netdns2.com. 86400 IN SSHFP 2 1 123456789abcdef67890123456789abcdef67890',
            'TALINK'        => 'talink.netdns2.com. 86400 IN TALINK c1.example.com. c3.example.com.',
            'TA'            => 'ta.netdns2.com. 86400 IN TA 21366 7 2 96eeb2ffd9b00cd4694e78278b5efdab0a80446567b69f634da078f0d90f01ba',
            'TLSA'          => [

                'name'  => '_443._tcp.www.netdns2.com',
                'res'   => '_443._tcp.www.netdns2.com. 86400 IN TLSA 1 1 2 92003ba34942dc74152e2f2c408d29eca5a520e7f2e06bb944f4dca346baf63c1b177615d466f6c4b71c216a50292bd58c9ebdd2f74e38fe51ffd48c43326cbc'
            ],
            'TXT'           => 'txt.netdns2.com. 86400 IN TXT "first record" "another records" "a third"',
            'URI'           => 'uri.netdns2.com. 86400 IN URI 10 1 "https://netdns2.com/about"',
            'WKS'           => 'wks.netdns2.com. 86400 IN WKS 128.8.1.14 6 21 25',
            'X25'           => 'x25.netdns2.com. 86400 IN X25 "311061700956"',
            'ZONEMD'        => 'zonemd.netdns2.com. 86400 IN ZONEMD 2018031500 1 1 febe3d4ce2ec2ffa4ba99d46cd69d6d29711e55217057bee7eb1a7b641a47ba7fed2dd5b97ae499fafa4f22c6bd647de'
        ];

        $r = new Net_DNS2_Resolver([ 'nameservers' => $ns ]);

        foreach($rrs as $rr => $expect)
        {
            $res = null;

            if (is_array($expect) == true)
            {
                $res = $r->query($expect['name'], $rr);
            } else
            {
                $res = $r->query(strtolower($rr) . '.netdns2.com', $rr);
            }

            $this->assertCount(1, $res->answer);
            $this->assertSame((is_array($expect) == true) ? $expect['res'] : $expect, strval($res->answer[0]));
        }
    }    
}