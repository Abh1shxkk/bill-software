<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Demo Organization
        $organization = Organization::where('code', 'DEMO2026')->first();

        if (!$organization) {
            $this->command->error('Organization with code DEMO2026 not found!');
            $this->command->info('Please run OrganizationAdminSeeder first.');
            return;
        }

        $this->command->info('Seeding suppliers for: ' . $organization->name);

        // Get all supplier data
        $suppliers = $this->getSupplierData();

        $count = 0;
        foreach ($suppliers as $supplierData) {
            // Add organization_id and status to each supplier
            $supplierData['organization_id'] = $organization->id;
            $supplierData['status'] = 'Y';
            $supplierData['is_deleted'] = 0;
            
            $supplier = Supplier::updateOrCreate(
                [
                    'name' => $supplierData['name'],
                    'organization_id' => $organization->id
                ],
                $supplierData
            );

            $count++;
            if ($count % 10 == 0) {
                $this->command->info("✓ Processed $count suppliers...");
            }
        }

        $this->command->info('');
        $this->command->info('Supplier seeding completed!');
        $this->command->info('Total suppliers seeded: ' . $count);
    }

    /**
     * Get supplier data array
     */
    private function getSupplierData(): array
    {
        return [
            // Skipping first supplier (A.K.MEDICINE AGENCIES) as requested
            ['code' => null, 'name' => 'AADI MEDICOSE', 'address' => 'PARMANAND PURAM, PRATAP BUILDING, SWARG ASHRAM ROAD, HAPUR - 245101', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => '100/20B/GZB/2008', 'dl_no_1' => null, 'gst_no' => '09AHWPJ0435F1Z7'],
            ['code' => null, 'name' => 'AADISREE MEDICAL PVT. LTD.', 'address' => 'G-1/108, PHASE-2 TRANSPORT NAGAR KANPUR ROAD LUCKNOW 226012 U.P.', 'telephone' => '0522-2996151', 'mobile' => null, 'email' => 'ampl_lko@yahoo.com', 'dl_no' => 'LKO/FDA-3824/14', 'dl_no_1' => null, 'gst_no' => '09AAMCA3112C1Z5'],
            ['code' => null, 'name' => 'AGARWAL ASOCIATES', 'address' => '12/A STATION ROAD LUCKNOW', 'telephone' => '0522-4043792', 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'AGGARWAL SURGICALS&PHARMACEUTICALS', 'address' => 'SHOP NO. 3, 1ST FLOOR CETRAL MARKET KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'MUT-2017/20B/000375', 'dl_no_1' => null, 'gst_no' => '09AIJPK4678D1ZZ'],
            ['code' => null, 'name' => 'ALLENBURG HEALTHCARE', 'address' => '2/86, VIRAM KHAND GOMTI NAGAR LKUCKNOW', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP3220B004204', 'dl_no_1' => 'UP3221B004185', 'gst_no' => '09AKNPA7509H1Z1'],
            ['code' => null, 'name' => 'AMAN PHARMA', 'address' => 'I-385 SHASTRI NAGAR MEERUT U.P 250004', 'telephone' => '09897766666', 'mobile' => null, 'email' => null, 'dl_no' => 'MRT-20B-118/14', 'dl_no_1' => 'MRT-21B-118/14', 'gst_no' => '09AKSPK4725C1Z0'],
            ['code' => null, 'name' => 'AMAR MEDISOLUTIONS PVT. LTD.', 'address' => '3, BHAGWAN DAS QUARTERS NEAR CLOCK TOWER DEHRADUN', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UA-DEH-137245', 'dl_no_1' => '137246', 'gst_no' => '05AAXCA0760D1ZS'],
            ['code' => null, 'name' => 'ANIL MEDICAL AGENCY', 'address' => '27A, NEW HARIDWAR COLONY AANEAR DEV BHOOMI HOSPITAL, HARIDWAR UTTARAKHAND', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => '20B/UA-HRD-113783', 'dl_no_1' => null, 'gst_no' => '05ACKPJ2370A1Z5'],
            ['code' => null, 'name' => 'APRICA HEALTHCARE', 'address' => 'AHEMDABAD', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B001329', 'dl_no_1' => 'UP1521B001321', 'gst_no' => '09AAFFS3059Q1ZH'],
            ['code' => null, 'name' => 'ARPIT DRUG DISTRIBUTORS', 'address' => 'D-5 MEERUT ROAD, INDUSTRIAL AREA, SITE-3, GHAZIABAD U.P.', 'telephone' => '01202723934', 'mobile' => null, 'email' => 'arpitd5@yahoo.co.in', 'dl_no' => '16/20B/428/03', 'dl_no_1' => '16/21B/428/03', 'gst_no' => null],
            ['code' => null, 'name' => 'ASHIRWAD SURGICALS & MEDICINE', 'address' => 'SHOP NO.21, FIRST FLOOR CENTRAL PLAZA KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => 'ashirwadsurgical514@gmail.com', 'dl_no' => 'UP1520B002106', 'dl_no_1' => 'UP1521B002100', 'gst_no' => '09HLKPS8372B1ZI'],
            ['code' => '00007', 'name' => 'ASHUTOSH MEDICOSE', 'address' => 'UPPER INDIA COMPLEX MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'ATHARVA PHARMA & SURGICAL', 'address' => 'B-10, BABA MEDI MALL KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B0000723', 'dl_no_1' => 'UP1521B0000720', 'gst_no' => '09ALGPT6063K1ZH'],
            ['code' => null, 'name' => 'ATUL MEDICAL AGENCY', 'address' => '196/1 FIRST FLOOR CENTRAL PLAZA BAN BATAN KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B000222', 'dl_no_1' => 'UP1521B000222', 'gst_no' => '09AJDPG3469K1ZX'],
            ['code' => null, 'name' => 'AVS MEDICAL STORE AGENCY', 'address' => 'L-246 GROUND FLOOR OPP. KHYATI SHASTRI NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B001649', 'dl_no_1' => 'UO1521B001644', 'gst_no' => '09DTJPS1236P1ZX'],
            ['code' => null, 'name' => 'B.R.ENTERPRISES', 'address' => 'K.B.L.MEDICAL COMPLEX KHAIR NAGAR MEERUT', 'telephone' => '01212421220', 'mobile' => null, 'email' => null, 'dl_no' => 'OBW-339/85', 'dl_no_1' => 'BW-340/85', 'gst_no' => null],
            ['code' => null, 'name' => 'BABA MEDICAL AGENCY', 'address' => 'OPP.E.S.I.DISPENSARY MODI NAGAR', 'telephone' => '08979111515', 'mobile' => null, 'email' => null, 'dl_no' => '125/20B-2008', 'dl_no_1' => '125/21B-2008', 'gst_no' => '09AYVPK8027G1ZU'],
            ['code' => null, 'name' => 'BADRI MEDICAL AGENCIES', 'address' => '114, ANKIT VIHAR PACHANDA ROAD MUZAFFAR NAGAR-251002 U.P.', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1220B001337', 'dl_no_1' => 'UP1221B001335', 'gst_no' => '09ESDPB8113A1ZA'],
            ['code' => null, 'name' => 'BADRINATH MEDICOSE', 'address' => 'BRIJ MARKETNEAR SHIV CHOWK, P.S.KOTWALI MUZAFFAR NAGAR U.P', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'MOZ-2017/20B/000616', 'dl_no_1' => null, 'gst_no' => '09AASFB5109P1ZS'],
            ['code' => null, 'name' => 'BALAJI ENTERPRISES', 'address' => 'HF-6, 159 - 1ST FLOOR BABA MEDI MALL', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B000151', 'dl_no_1' => 'UP1521B000151', 'gst_no' => '09AAOFB8859Q1Z7'],
            ['code' => null, 'name' => 'BANSI MEDICINE AGENCY', 'address' => 'SHOP NO.7 BAN BATAN CENTRAL MARKET KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'MUT-2015/20B/00015', 'dl_no_1' => null, 'gst_no' => '09ASYPG2442E1ZK'],
            ['code' => null, 'name' => 'BHARAT MEDICAL STORE', 'address' => 'KOTHI GATE HAPUR', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'HUP-2017/20B/00023', 'dl_no_1' => null, 'gst_no' => '09ABWPG4699J1ZN'],
            ['code' => null, 'name' => 'BHARAT MEDICOSE', 'address' => '79, ZILA PARISHAD MARKET 1ST FLOOR MUZAFFAR NAGAR', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => '20B-342/99', 'dl_no_1' => '21B-343/99', 'gst_no' => '09ACMPM0891P1ZR'],
            ['code' => null, 'name' => 'BHAWANI MEDICAL AGENCY', 'address' => 'SHYAM MARKET OPP.VIJAYA BANK MODINAGAR U.P.', 'telephone' => '9319450629', 'mobile' => null, 'email' => null, 'dl_no' => '127/20B/GZB/04', 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'BHUSHAN MEDICOS', 'address' => 'SHOP.NO.9 CENTRAL PLAZA KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'OBW-145/2004', 'dl_no_1' => 'BW-146/2004', 'gst_no' => null],
            ['code' => null, 'name' => 'BRAJ & SONS MEDICONE CO.', 'address' => 'SHOP.NO.-15 MATEEN COMPLEX KHAIR NAGAR MEERUT', 'telephone' => '09675776773', 'mobile' => null, 'email' => null, 'dl_no' => 'MRT-20B-18/15', 'dl_no_1' => 'MRT-21B-18/2015', 'gst_no' => '09AMDPG4984E1ZY'],
            ['code' => null, 'name' => 'BRIJ PHARMA AGENCIES', 'address' => '40, ZILA PARISHAD MARKET, MUZAFFARNAGAR', 'telephone' => null, 'mobile' => null, 'email' => 'brijpharma2012@gmail.com', 'dl_no' => '20B-48', 'dl_no_1' => '21B-48', 'gst_no' => '09AALFB6425F1ZF'],
            ['code' => null, 'name' => 'BRIJ PHARMACEUTICALS', 'address' => 'NEAR RAGHAV FARM, ITI COLLEGE CHAKKAR CHAURAHA BIJNOR U.P. 246701', 'telephone' => null, 'mobile' => null, 'email' => 'brijpharmaceuticals6@gmail.com', 'dl_no' => 'UP2021B000257', 'dl_no_1' => 'UP2020B000257', 'gst_no' => '09AAVFB7988D1ZN'],
            ['code' => null, 'name' => 'C.M.R.LIFESCIENCS PVT.LTD.', 'address' => 'C-20, BASEMENT COMMUNITY CENTRE JANAKPURI NEW DELHI - 110058', 'telephone' => '011-49785767', 'mobile' => null, 'email' => 'amansharma@cmr-lifesciences.co', 'dl_no' => 'WLF20B2022DL000709', 'dl_no_1' => null, 'gst_no' => '07AAECC3350J2ZR'],
            ['code' => null, 'name' => 'CHEAP MEDICAL STORE', 'address' => 'KHAIR NAGAR BAZAR MEERUT', 'telephone' => '0121-2519434', 'mobile' => null, 'email' => 'cheapmedicalstores@yahoo.co.in', 'dl_no' => 'UP1520B001832', 'dl_no_1' => 'UP1521B001826', 'gst_no' => '09ABAPC1195R1ZC'],
            ['code' => null, 'name' => 'D.P.PHARMA', 'address' => '190/151, ANSARI MARG, NEAR KALIKA MANDIR DEHRADUN-248001', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP-DEH-108151', 'dl_no_1' => 'UA-DEH-108150', 'gst_no' => '05AAKFD2173R1Z0'],
            ['code' => null, 'name' => 'DAKSH ENTERPRISES', 'address' => 'AF-2, SHIVAM COMPLEX 1 ST FLOOR KHAIR NAGAR MEERUT', 'telephone' => '8859019997', 'mobile' => null, 'email' => null, 'dl_no' => 'MRT-20B-91/13', 'dl_no_1' => 'MRT-21B-91/13', 'gst_no' => '09AHKPG5612P1ZW'],
            ['code' => '09', 'name' => 'DAKSH ENTERPRISES SAHARANPUR', 'address' => 'KISHAN PURA SAHARANPUR', 'telephone' => '01326451122', 'mobile' => null, 'email' => null, 'dl_no' => '20B-SRE/116/2013', 'dl_no_1' => null, 'gst_no' => '09AAFFD9002N1Z7'],
            ['code' => null, 'name' => 'DHARAM MEDICAL AGENCY', 'address' => 'OPP. NAYYAR PALACE, BOUNDRY RD. LALKURTI, MEERUT CANTT.', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'DR.REDDYS LABORATORIES LIMITED', 'address' => 'D-27, FRONT PORTION, BASEMENT G-FLOOR OKHLA INDUSTRIAL AREA PHASE-1, NEW DELHI', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => '07AAACD7999Q1ZM'],
            ['code' => null, 'name' => 'DRUG HOUSE', 'address' => '26-PRACHI COMPLEX KHAIR NAGAR MEERUT', 'telephone' => '0121-2527974', 'mobile' => null, 'email' => null, 'dl_no' => 'OBW-14-10-98', 'dl_no_1' => 'BW-14-10-98', 'gst_no' => null],
            ['code' => null, 'name' => 'DUA DRUG DISTRIBUTORS', 'address' => 'KBL MEDICAL COMPLEX KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B001899', 'dl_no_1' => 'UP1521B001893', 'gst_no' => '09ACHPD7353A1ZX'],
            ['code' => null, 'name' => 'DUA MEDICOS', 'address' => 'KISHAN PURA SAHARN PUR', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => '12/2007/U/WB', 'dl_no_1' => '12/2007/U/WNB', 'gst_no' => '09AENPD5620G1ZL'],
            ['code' => null, 'name' => 'ELITE HEALTHCARE & PLASTICS', 'address' => '54-B-PRAHLAD NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => 'elitehealthcare09@gmail.com', 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => '09ANIPD5128P1ZL'],
            ['code' => null, 'name' => 'EMKAY MEDICAL AGENCY', 'address' => 'VIVEK VIHAR OPP. GANDHI EYE HOSPITAL RAM GHAT ROAD ALIGARH-202001', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => '231/20B/ALG/2010', 'dl_no_1' => null, 'gst_no' => '09ANTPG9449H1Z8'],
            ['code' => null, 'name' => 'G.R.MEDICOSE', 'address' => 'MATTEN MARKET, KHAIR NAGAR, MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'G.S.B. HELATHCARE', 'address' => '1 ST FLOR 1&2 SHOP RAMLEELE MAIDAN TEHSEEL DISTRICT BIJNOR U.P.', 'telephone' => '9634067555', 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => null, 'name' => 'G.S.DISTRIBUTORS-UP', 'address' => 'SHIV NAGAR BERI BAGH NEAR CHAND GAS AGENCY SAHARANPUR', 'telephone' => '0132-2662002', 'mobile' => null, 'email' => null, 'dl_no' => '20B17/3C/U/WNB', 'dl_no_1' => '21B/17/3C/U/WB', 'gst_no' => null],
            ['code' => '00051', 'name' => 'GARG MEDICINE COMPANY', 'address' => '9, KAMLA MARKET KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => 'UP1520B001176', 'dl_no_1' => null, 'gst_no' => '09ADDPK3679H1Z9'],
            ['code' => '00008', 'name' => 'JAIN MEDICINE CO.', 'address' => 'SETHI MARKET KHAIR NAGAR MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => '00034', 'name' => 'KAMAL ENTERPRISES', 'address' => 'MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => '00186', 'name' => 'KARUN ENTERPRISES', 'address' => 'SE-7, ROOM NO.-1 SHASTRI NAGAR, GHAZIABAD - 02', 'telephone' => '2764845', 'mobile' => null, 'email' => null, 'dl_no' => '89/20B/GZB/02', 'dl_no_1' => '89/21B/GZB/02', 'gst_no' => '09AALPJ6463H1ZC'],
            ['code' => '00053', 'name' => 'M.G MEDICOSE', 'address' => 'MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => '00035', 'name' => 'NARANG MEDICINE COMPANY', 'address' => 'MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => '00036', 'name' => 'VEER MEDICINE COMPANY', 'address' => 'MEERUT', 'telephone' => null, 'mobile' => null, 'email' => null, 'dl_no' => null, 'dl_no_1' => null, 'gst_no' => null],
            ['code' => 'IPL', 'name' => 'INTAS PHARMACEUTICALS LTD.', 'address' => '87-B HILL SETEET MEERUT', 'telephone' => '0121-2664362', 'mobile' => null, 'email' => 'panchsheel@intaspharma.com', 'dl_no' => 'UP1520B001762', 'dl_no_1' => 'UP1521B001756', 'gst_no' => '09AAACI5120L1ZM'],
            // Add more suppliers as needed - this is a sample with key suppliers
        ];
    }
}
