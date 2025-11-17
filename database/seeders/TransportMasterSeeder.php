<?php

namespace Database\Seeders;

use App\Models\TransportMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transports = [
            ['name' => 'Blue Dart Express', 'address' => 'Plot 12, Industrial Area\nMumbai, Maharashtra 400001', 'alter_code' => 'BD001', 'telephone' => '022-12345678', 'email' => 'info@bluedart.com', 'mobile' => '9876543210', 'gst_no' => '27AABCU9603R1ZM', 'status' => 'Active', 'vehicle_no' => 'MH-01-AB-1234', 'trans_mode' => 'Road'],
            ['name' => 'DTDC Courier', 'address' => 'Sector 15, Transport Hub\nDelhi, Delhi 110001', 'alter_code' => 'DT002', 'telephone' => '011-23456789', 'email' => 'contact@dtdc.com', 'mobile' => '9876543211', 'gst_no' => '07AABCD1234E1Z5', 'status' => 'Active', 'vehicle_no' => 'DL-02-BC-5678', 'trans_mode' => 'Road'],
            ['name' => 'Professional Couriers', 'address' => 'Zone 8, Logistics Park\nAhmedabad, Gujarat 380001', 'alter_code' => 'PC003', 'telephone' => '079-34567890', 'email' => 'info@procourier.com', 'mobile' => '9876543212', 'gst_no' => '24AABCP1234F1Z6', 'status' => 'Active', 'vehicle_no' => 'GJ-03-CD-9012', 'trans_mode' => 'Road'],
            ['name' => 'First Flight Couriers', 'address' => 'Building 5, Tech City\nBangalore, Karnataka 560001', 'alter_code' => 'FF004', 'telephone' => '080-45678901', 'email' => 'support@firstflight.com', 'mobile' => '9876543213', 'gst_no' => '29AABCF1234G1Z7', 'status' => 'Active', 'vehicle_no' => 'KA-04-DE-3456', 'trans_mode' => 'Air'],
            ['name' => 'Gati Packers', 'address' => 'Warehouse 3, Port Area\nChennai, Tamil Nadu 600001', 'alter_code' => 'GP005', 'telephone' => '044-56789012', 'email' => 'info@gati.com', 'mobile' => '9876543214', 'gst_no' => '33AABCG1234H1Z8', 'status' => 'Active', 'vehicle_no' => 'TN-05-EF-7890', 'trans_mode' => 'Road'],
            
            ['name' => 'VRL Logistics', 'address' => 'Station Road, Transport Nagar\nPune, Maharashtra 411001', 'alter_code' => 'VL006', 'telephone' => '020-67890123', 'email' => 'contact@vrl.com', 'mobile' => '9876543215', 'gst_no' => '27AABCV1234I1Z9', 'status' => 'Active', 'vehicle_no' => 'MH-06-FG-1234', 'trans_mode' => 'Road'],
            ['name' => 'TCI Express', 'address' => 'Sector 22, Industrial Zone\nHyderabad, Telangana 500001', 'alter_code' => 'TC007', 'telephone' => '040-78901234', 'email' => 'info@tciexpress.com', 'mobile' => '9876543216', 'gst_no' => '36AABCT1234J1ZA', 'status' => 'Active', 'vehicle_no' => 'TS-07-GH-5678', 'trans_mode' => 'Rail'],
            ['name' => 'Safexpress', 'address' => 'Plot 45, Cargo Complex\nKolkata, West Bengal 700001', 'alter_code' => 'SX008', 'telephone' => '033-89012345', 'email' => 'support@safexpress.com', 'mobile' => '9876543217', 'gst_no' => '19AABCS1234K1ZB', 'status' => 'Active', 'vehicle_no' => 'WB-08-HI-9012', 'trans_mode' => 'Road'],
            ['name' => 'Delhivery', 'address' => 'Warehouse 12, Logistics Hub\nJaipur, Rajasthan 302001', 'alter_code' => 'DL009', 'telephone' => '0141-90123456', 'email' => 'info@delhivery.com', 'mobile' => '9876543218', 'gst_no' => '08AABCD1234L1ZC', 'status' => 'Active', 'vehicle_no' => 'RJ-09-IJ-3456', 'trans_mode' => 'Road'],
            ['name' => 'Ecom Express', 'address' => 'Zone 5, Distribution Center\nLucknow, Uttar Pradesh 226001', 'alter_code' => 'EC010', 'telephone' => '0522-01234567', 'email' => 'contact@ecomexpress.com', 'mobile' => '9876543219', 'gst_no' => '09AABCE1234M1ZD', 'status' => 'Active', 'vehicle_no' => 'UP-10-JK-7890', 'trans_mode' => 'Road'],
            
            ['name' => 'Shadowfax', 'address' => 'Building 8, Tech Park\nNoida, Uttar Pradesh 201301', 'alter_code' => 'SF011', 'telephone' => '0120-12345678', 'email' => 'info@shadowfax.com', 'mobile' => '9876543220', 'gst_no' => '09AABCS1234N1ZE', 'status' => 'Active', 'vehicle_no' => 'UP-11-KL-1234', 'trans_mode' => 'Road'],
            ['name' => 'Xpressbees', 'address' => 'Sector 18, Logistics Zone\nGurgaon, Haryana 122001', 'alter_code' => 'XB012', 'telephone' => '0124-23456789', 'email' => 'support@xpressbees.com', 'mobile' => '9876543221', 'gst_no' => '06AABCX1234O1ZF', 'status' => 'Active', 'vehicle_no' => 'HR-12-LM-5678', 'trans_mode' => 'Road'],
            ['name' => 'Ekart Logistics', 'address' => 'Plot 25, Warehouse District\nIndore, Madhya Pradesh 452001', 'alter_code' => 'EK013', 'telephone' => '0731-34567890', 'email' => 'info@ekartlogistics.com', 'mobile' => '9876543222', 'gst_no' => '23AABCE1234P1ZG', 'status' => 'Active', 'vehicle_no' => 'MP-13-MN-9012', 'trans_mode' => 'Road'],
            ['name' => 'Rivigo', 'address' => 'Zone 12, Transport Hub\nNagpur, Maharashtra 440001', 'alter_code' => 'RV014', 'telephone' => '0712-45678901', 'email' => 'contact@rivigo.com', 'mobile' => '9876543223', 'gst_no' => '27AABCR1234Q1ZH', 'status' => 'Active', 'vehicle_no' => 'MH-14-NO-3456', 'trans_mode' => 'Road'],
            ['name' => 'Mahindra Logistics', 'address' => 'Sector 30, Industrial Area\nSurat, Gujarat 395001', 'alter_code' => 'ML015', 'telephone' => '0261-56789012', 'email' => 'info@mahindralogistics.com', 'mobile' => '9876543224', 'gst_no' => '24AABCM1234R1ZI', 'status' => 'Active', 'vehicle_no' => 'GJ-15-OP-7890', 'trans_mode' => 'Road'],
            
            ['name' => 'Allcargo Logistics', 'address' => 'Port Zone, Shipping Area\nVisakhapatnam, AP 530001', 'alter_code' => 'AL016', 'telephone' => '0891-67890123', 'email' => 'support@allcargo.com', 'mobile' => '9876543225', 'gst_no' => '37AABCA1234S1ZJ', 'status' => 'Active', 'vehicle_no' => 'AP-16-PQ-1234', 'trans_mode' => 'Ship'],
            ['name' => 'Container Corporation', 'address' => 'Railway Yard, Cargo Terminal\nBhopal, Madhya Pradesh 462001', 'alter_code' => 'CC017', 'telephone' => '0755-78901234', 'email' => 'info@concorindia.com', 'mobile' => '9876543226', 'gst_no' => '23AABCC1234T1ZK', 'status' => 'Active', 'vehicle_no' => 'MP-17-QR-5678', 'trans_mode' => 'Rail'],
            ['name' => 'SpiceJet Cargo', 'address' => 'Airport Complex, Cargo Terminal\nCochin, Kerala 682001', 'alter_code' => 'SJ018', 'telephone' => '0484-89012345', 'email' => 'cargo@spicejet.com', 'mobile' => '9876543227', 'gst_no' => '32AABCS1234U1ZL', 'status' => 'Active', 'vehicle_no' => 'KL-18-RS-9012', 'trans_mode' => 'Air'],
            ['name' => 'IndiGo CarGo', 'address' => 'Terminal 2, Airport Road\nThiruvananthapuram, Kerala 695001', 'alter_code' => 'IC019', 'telephone' => '0471-90123456', 'email' => 'cargo@goindigo.in', 'mobile' => '9876543228', 'gst_no' => '32AABCI1234V1ZM', 'status' => 'Active', 'vehicle_no' => 'KL-19-ST-3456', 'trans_mode' => 'Air'],
            ['name' => 'Maersk Shipping', 'address' => 'Dock 5, Port Area\nMumbai, Maharashtra 400001', 'alter_code' => 'MS020', 'telephone' => '022-01234567', 'email' => 'info@maersk.com', 'mobile' => '9876543229', 'gst_no' => '27AABCM1234W1ZN', 'status' => 'Inactive', 'vehicle_no' => 'MH-20-TU-7890', 'trans_mode' => 'Ship'],
            
            ['name' => 'Om Logistics', 'address' => 'Sector 45, Transport Nagar\nChandigarh, Chandigarh 160001', 'alter_code' => 'OL021', 'telephone' => '0172-12345678', 'email' => 'info@omlogistics.com', 'mobile' => '9876543230', 'gst_no' => '04AABCO1234X1ZO', 'status' => 'Active', 'vehicle_no' => 'CH-21-UV-1234', 'trans_mode' => 'Road'],
            ['name' => 'Agarwal Packers', 'address' => 'Plot 18, Industrial Zone\nLudhiana, Punjab 141001', 'alter_code' => 'AP022', 'telephone' => '0161-23456789', 'email' => 'contact@agarwalpackers.com', 'mobile' => '9876543231', 'gst_no' => '03AABCA1234Y1ZP', 'status' => 'Active', 'vehicle_no' => 'PB-22-VW-5678', 'trans_mode' => 'Road'],
            ['name' => 'VRL Packers', 'address' => 'Station Road, Transport Hub\nVijayawada, AP 520001', 'alter_code' => 'VP023', 'telephone' => '0866-34567890', 'email' => 'info@vrlpackers.com', 'mobile' => '9876543232', 'gst_no' => '37AABCV1234Z1ZQ', 'status' => 'Active', 'vehicle_no' => 'AP-23-WX-9012', 'trans_mode' => 'Road'],
            ['name' => 'Leo Packers', 'address' => 'Zone 8, Logistics Park\nCoimbatore, Tamil Nadu 641001', 'alter_code' => 'LP024', 'telephone' => '0422-45678901', 'email' => 'support@leopackers.com', 'mobile' => '9876543233', 'gst_no' => '33AABCL1234A2ZR', 'status' => 'Active', 'vehicle_no' => 'TN-24-XY-3456', 'trans_mode' => 'Road'],
            ['name' => 'Shree Maruti', 'address' => 'Warehouse 15, Port Road\nMangalore, Karnataka 575001', 'alter_code' => 'SM025', 'telephone' => '0824-56789012', 'email' => 'info@shreemaruti.com', 'mobile' => '9876543234', 'gst_no' => '29AABCS1234B2ZS', 'status' => 'Active', 'vehicle_no' => 'KA-25-YZ-7890', 'trans_mode' => 'Road'],
            
            ['name' => 'Balaji Cargo', 'address' => 'Sector 12, Industrial Area\nRanchi, Jharkhand 834001', 'alter_code' => 'BC026', 'telephone' => '0651-67890123', 'email' => 'contact@balajicargo.com', 'mobile' => '9876543235', 'gst_no' => '20AABCB1234C2ZT', 'status' => 'Active', 'vehicle_no' => 'JH-26-ZA-1234', 'trans_mode' => 'Road'],
            ['name' => 'Shree Ganesh', 'address' => 'Plot 22, Transport Zone\nPatna, Bihar 800001', 'alter_code' => 'SG027', 'telephone' => '0612-78901234', 'email' => 'info@shreeganesh.com', 'mobile' => '9876543236', 'gst_no' => '10AABCS1234D2ZU', 'status' => 'Active', 'vehicle_no' => 'BR-27-AB-5678', 'trans_mode' => 'Road'],
            ['name' => 'Raj Transport', 'address' => 'Zone 5, Logistics Hub\nJodhpur, Rajasthan 342001', 'alter_code' => 'RT028', 'telephone' => '0291-89012345', 'email' => 'support@rajtransport.com', 'mobile' => '9876543237', 'gst_no' => '08AABCR1234E2ZV', 'status' => 'Active', 'vehicle_no' => 'RJ-28-BC-9012', 'trans_mode' => 'Road'],
            ['name' => 'Shivam Logistics', 'address' => 'Building 10, Cargo Complex\nUdaipur, Rajasthan 313001', 'alter_code' => 'SL029', 'telephone' => '0294-90123456', 'email' => 'info@shivamlogistics.com', 'mobile' => '9876543238', 'gst_no' => '08AABCS1234F2ZW', 'status' => 'Active', 'vehicle_no' => 'RJ-29-CD-3456', 'trans_mode' => 'Road'],
            ['name' => 'Krishna Cargo', 'address' => 'Sector 18, Transport Nagar\nAgra, Uttar Pradesh 282001', 'alter_code' => 'KC030', 'telephone' => '0562-01234567', 'email' => 'contact@krishnacargo.com', 'mobile' => '9876543239', 'gst_no' => '09AABCK1234G2ZX', 'status' => 'Active', 'vehicle_no' => 'UP-30-DE-7890', 'trans_mode' => 'Road'],
        ];

        foreach ($transports as $transport) {
            TransportMaster::create($transport);
        }
    }
}
