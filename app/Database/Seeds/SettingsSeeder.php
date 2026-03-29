<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder {
  //---------------------------------------------------------------------------
  /**
   * Seed the 'settings' table.
   */
  public function run() {
    $records = array(
      [ 'key' => 'alerts', 'value' => 'all' ],
      [ 'key' => 'allowRegistration', 'value' => '1' ],
      [ 'key' => 'allowRemembering', 'value' => '1' ],
      [ 'key' => 'applicationUrl', 'value' => 'https://localhost/MTS-app/public' ],
      [ 'key' => 'applicationName', 'value' => 'Modern Tailoring System' ],
      [ 'key' => 'autocloseAlertSuccess', 'value' => '1' ],
      [ 'key' => 'autocloseAlertWarning', 'value' => '0' ],
      [ 'key' => 'autocloseAlertDanger', 'value' => '0' ],
      [ 'key' => 'autocloseDelay', 'value' => '5000' ],
      [ 'key' => 'caching', 'value' => '1' ],
      [ 'key' => 'cookieConsent', 'value' => '1' ],
      [ 'key' => 'dataPrivacyPolicy', 'value' => '1' ],
      [ 'key' => 'defaultLanguage', 'value' => 'en' ],
      [ 'key' => 'defaultTheme', 'value' => 'light' ],
      [ 'key' => 'emailNotifications', 'value' => '0' ],
      [ 'key' => 'emailFrom', 'value' => 'Modern Tailoring System' ],
      [ 'key' => 'emailReply', 'value' => 'asilahzarifah0@gmail.com' ],
      [ 'key' => 'emailSMTP', 'value' => '' ],
      [ 'key' => 'emailSMTPhost', 'value' => '' ],
      [ 'key' => 'emailSMTPport', 'value' => '' ],
      [ 'key' => 'emailSMTPusername', 'value' => '' ],
      [ 'key' => 'emailSMTPpassword', 'value' => '' ],
      [ 'key' => 'emailSMTPCrypto', 'value' => '' ],
      [ 'key' => 'emailSMTPAnonymous', 'value' => '0' ],
      [ 'key' => 'font', 'value' => 'opensans' ],
      [ 'key' => 'footerCopyrightName', 'value' => 'Lewe' ],
      [ 'key' => 'footerCopyrightUrl', 'value' => 'https://www.lewe.com' ],
      [ 'key' => 'footerSocialLinks', 'value' => 'https://www.linkedin.com/in/george-lewe-a9ab6411b;https://www.xing.com/profile/George_Lewe;https://twitter.com/gekale;https://www.paypal.me/GeorgeLewe' ],
      [ 'key' => 'gdprOrganization', 'value' => 'ACME Inc.' ],
      [ 'key' => 'gdprController', 'value' => 'ACME Inc.
123 Street
Hometown, XY 4567
Germany
Email: info@acme.com' ],
      [ 'key' => 'gdprPrivacyOfficer', 'value' => 'John Doe
Phone: +49 555 12345
Email: john.doe@acme.com' ],
      [ 'key' => 'googleAnalytics', 'value' => '0' ],
      [ 'key' => 'googleAnalyticsId', 'value' => '' ],
      [ 'key' => 'htmlDescription', 'value' => 'MTS is a system for managing the tailoring process of Small and Medium Enterprises. It is a PHP application framework boilerplate based on CodeIgniter 4.' ],
      [ 'key' => 'htmlKeywords', 'value' => 'Modern Tailoring System, tailoring, SME' ],
      [ 'key' => 'imprint', 'value' => '1' ],
      [ 'key' => 'licenseKey', 'value' => '' ],
      [ 'key' => 'logAuth', 'value' => '1' ],
      [ 'key' => 'logDatabase', 'value' => '1' ],
      [ 'key' => 'logGroup', 'value' => '1' ],
      [ 'key' => 'logLog', 'value' => '1' ],
      [ 'key' => 'logPermission', 'value' => '1' ],
      [ 'key' => 'logRole', 'value' => '1' ],
      [ 'key' => 'logSettings', 'value' => '1' ],
      [ 'key' => 'logUser', 'value' => '1' ],
      [ 'key' => 'logColorAuth', 'value' => '' ],
      [ 'key' => 'logColorDatabase', 'value' => '' ],
      [ 'key' => 'logColorGroup', 'value' => '' ],
      [ 'key' => 'logColorLog', 'value' => '' ],
      [ 'key' => 'logColorPermission', 'value' => '' ],
      [ 'key' => 'logColorRole', 'value' => '' ],
      [ 'key' => 'logColorSettings', 'value' => '' ],
      [ 'key' => 'logColorUser', 'value' => '' ],
      [ 'key' => 'matomoAnalytics', 'value' => '0' ],
      [ 'key' => 'matomoAnalyticsUrl', 'value' => '' ],
      [ 'key' => 'matomoAnalyticsId', 'value' => '' ],
      [ 'key' => 'noCaching', 'value' => '0' ],
      [ 'key' => 'require2fa', 'value' => '0' ],
      [ 'key' => 'robots', 'value' => 'noindex' ],
      [ 'key' => 'timezone', 'value' => 'Europe/Berlin' ],
      [ 'key' => 'underMaintenance', 'value' => '0' ],
      [ 'key' => 'versionCheck', 'value' => '1' ],
      [ 'key' => 'welcomeText', 'value' => '<h3><img alt="" src="images/icons/logo.png" style="float:left; height:128px; margin-bottom:24px; margin-right:24px; width:128px" />Welcome to MTS</h3>
<p>MTS is a system for managing the tailoring process of Small and Medium Enterprises. It is a PHP application framework boilerplate based on CodeIgniter 4.</p>
<p>Select Login from the User menu to login and use the following accounts to give this demo a test drive:</p>
<p><strong>Admin account:</strong></p>
<p>admin/Qwer!1234</p>
<p><strong>User accounts:</strong></p>
<p>ccarl/Qwer!1234<br />
blightyear/Qwer!1234<br />
dduck/Qwer!1234<br />
sgonzalez/Qwer!1234<br />
phead/Qwer!1234<br />
mmouse/Qwer!1234<br />
mimouse/Qwer!1234<br />
sman/Qwer!1234
</p>' ],
    );

    //
    // Simple Queries
    //
    // $this->db->query("INSERT INTO users (username, email) VALUES(:username:, :email:)", $data);

    //
    // Insert records
    //
    foreach ($records as $record) {
      $this->db->table('settings')->insert($record);
    }
  }
}
