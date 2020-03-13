<?php
/**
 * Software License Manager
 *
 * @author George Lewe <george@lewe.com>
 * @version 1.0.0
 * @source https://github.com/glewe/software-license-manager-class
 * @license https://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * You can use this class in any PHP application to communicate with a WordPress
 * based license server using the Software License Manager plugin:
 * https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress
 */
class SoftwareLicenseManager
{
   //
   // This variable specifies the value which is set in the license manager 
   // plugin settings page as: "Secret Key for License Verification Requests".
   //
   const SECRET_KEY = '5421048138b321.90068894';

   //
   // This variable specifies the URL of your server where the license manager
   // plugin is installed on. Your plugin from a customer’s site will be 
   // communicating with this server to activate or deactivate license keys.
   //
   const LICENSE_SERVER = 'https://mylicenseserver.com';

   //
   // This variable provides a reference label for the licenses which will be 
   // issued. Therefore you should enter something specific to describe what 
   // the licenses issued are pertaining to.
   //
   const ITEM_REFERENCE = 'My Licensed Item';
   
   //
   // Private variable holding the linces key itself.
   // Set with setKey(), read with getKey() method.
   //
   private $key = '';

   //
   // Language array for thje details display
   //
   private $lang = array();

   //
   // JSON reponse from the license server
   //
   public $details;

   // ---------------------------------------------------------------------
   /**
    * Constructor
    */
   public function __construct()
   {
      //
      // Fill language array
      //
      $this->lang['lic_active'] = 'Active License';
      $this->lang['lic_active_subject'] = 'This is an active license for this domain. Awesome!';
      $this->lang['lic_active_unregistered_subject'] = 'This is an active license but not registered for this domain.';
      $this->lang['lic_alert_activation_fail'] = 'The following error occurred while trying to activate your license:';
      $this->lang['lic_alert_activation_success'] = 'Your license was successfully activated for this domain.';
      $this->lang['lic_alert_registration_fail'] = 'The following error occurred while trying to register your domain to your license:';
      $this->lang['lic_alert_registration_success'] = 'Your domain was successfully registered to your license.';
      $this->lang['lic_alert_deregistration_fail'] = 'The following error occurred while trying to deregister your domain from your license:';
      $this->lang['lic_alert_deregistration_success'] = 'Your domain was successfully deregistered from your license.';
      $this->lang['lic_blocked'] = 'Blocked License';
      $this->lang['lic_blocked_subject'] = 'This license is blocked.';
      $this->lang['lic_blocked_help'] = 'Please contact your administrator to unblock this license.';
      $this->lang['close_this_message'] = 'Close this message';
      $this->lang['lic_company'] = 'Company';
      $this->lang['lic_date_created'] = 'Date Created';
      $this->lang['lic_date_expiry'] = 'Date Expiry';
      $this->lang['lic_date_renewed'] = 'Date Renewed';
      $this->lang['lic_daysleft'] = 'days left';
      $this->lang['lic_details'] = 'License Details';
      $this->lang['lic_email'] = 'E-mail';
      $this->lang['lic_expired'] = 'Expired License';
      $this->lang['lic_expired_subject'] = 'This license has expired.';
      $this->lang['lic_expired_help'] = 'Please contact your administrator to renew this license.';
      $this->lang['lic_expiringsoon'] = 'License Expiry Warning';
      $this->lang['lic_expiringsoon_subject'] = 'Your license will expire in %d days.';
      $this->lang['lic_expiringsoon_help'] = 'Please contact your administrator to renew this license in time.';
      $this->lang['lic_invalid'] = 'Invalid License';
      $this->lang['lic_invalid_subject'] = 'No license key was found or it is invalid.';
      $this->lang['lic_invalid_text'] = 'This instance is unregistered or a ucfirst license key was not entered and activated yet.';
      $this->lang['lic_invalid_help'] = 'Please contact the administrator to obtain a valid license.';
      $this->lang['lic_key'] = 'License Key';
      $this->lang['lic_name'] = 'Licensee';
      $this->lang['lic_max_allowed_domains'] = 'Maximum Allowed Domains';
      $this->lang['lic_pending'] = 'Pending License';
      $this->lang['lic_pending_subject'] = 'This license is registered but not activated yet.';
      $this->lang['lic_pending_help'] = 'Please contact your administrator to activate this license.';
      $this->lang['lic_registered_domains'] = 'Registered Domains';
      $this->lang['lic_status'] = 'Status';
      $this->lang['lic_product'] = 'Product';
      $this->lang['lic_unregistered'] = 'Unregistered License';
      $this->lang['lic_unregistered_subject'] = 'The license key of this instance is not registered for this domain.';
      $this->lang['lic_unregistered_help'] = 'Please contact the administrator to register this domain or obtain a valid license.';
   }

   // ---------------------------------------------------------------------------
   /**
    * Activates a license (and registers the domain the request is coming from)
    *
    * @return JSON
    */
   function activate()
   {
      $parms = array(
         'slm_action' => 'slm_activate',
         'secret_key' => self::SECRET_KEY,
         'license_key' => $this->key,
         'registered_domain' => $_SERVER['SERVER_NAME'],
         'item_reference' => urlencode(self::ITEM_REFERENCE),
      );

      $response = $this->callAPI('GET', self::LICENSE_SERVER, $parms);
      $response = json_decode($response);
      
      if ( ! $response ){
          $response = (object) array('result' => 'error','message' => 'Unexpected Error! The query returned with an error.');
      }

      return $response;
   }

   // ---------------------------------------------------------------------------
   /**
    * API Call
    *
    * @param string $method  POST, PUT, GET, ...
    * @param string $url     API host URL
    * @param array  $parms   URL paramater: array("param" => "value") ==> index.php?param=value
    * @return JSON
    */
   function callAPI($method, $url, $data = false)
   {
      $curl = curl_init();

      switch (strtoupper($method))
      {
         case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
               curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
         case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
         default:
            if ($data)
               $url = sprintf("%s?%s", $url, http_build_query($data));
      }

      // Optional Authentication:
      // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

      // Optional Debugging:
      // $query = LICENSE_SERVER . '?slm_action=' . $data['slm_action'] . '&amp;secret_key=' . $data['secret_key'] . '&amp;license_key=' . $data['license_key'] . '&amp;registered_domain=' . $data['registered_domain'] . '&amp;item_reference=' . $data['item_reference'];
      // die($query);

      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $result = curl_exec($curl);
      curl_close($curl);

      return $result;
   }

   // ---------------------------------------------------------------------------
   /**
    * Deactivates a license (deregisters the domain the request is coming from)
    *
    * @return JSON
    */
   function deactivate()
   {
      $parms = array(
         'slm_action' => 'slm_deactivate',
         'secret_key' => self::SECRET_KEY,
         'license_key' => $this->key,
         'registered_domain' => $_SERVER['SERVER_NAME'],
         'item_reference' => urlencode(self::ITEM_REFERENCE),
      );

      $response = $this->callAPI('GET', self::LICENSE_SERVER, $parms);
      $response = json_decode($response);
      
      if ( ! $response ){
          $response = (object) array('result' => 'error','message' => 'Unexpected Error! The query returned with an error.');
      }

      return $response;
   }

   // ---------------------------------------------------------------------------
   /**
    * Checks whether the current domain is registered
    *
    * @return boolean
    */
   function domainRegistered()
   {
      // if (!$this->readKey()) return false; // Enable if using the readKey() method

      if (count($this->details->registered_domains)) {
         foreach($this->details->registered_domains as $domain) {
            if ($domain->registered_domain == $_SERVER['SERVER_NAME']) return true;
         }
         return false;
      }
      else {
         return false;
      }
   }
 
   // ---------------------------------------------------------------------------
   /**
    * Returns the days until expiry
    *
    * @return integer
    */
   function daysToExpiry()
   {
      $todayDate = new DateTime('now');
      $expiryDate = new DateTime($this->details->date_expiry);
      $daysToExpiry = $todayDate->diff($expiryDate);
      return intval($daysToExpiry->format('%R%a'));
   }

   // ---------------------------------------------------------------------------
   /**
    * Loads the license information from license server
    *
    * @return JSON Saved in $this->details ucfirstty
    */
   function load()
   {
      $parms = array(
         'slm_action' => 'slm_check',
         'secret_key' => self::SECRET_KEY,
         'license_key' => $this->key,
      );
      $response = $this->callAPI('GET', self::LICENSE_SERVER, $parms);
      $response = json_decode($response);
      $this->details = $response;
   }
 
   // ---------------------------------------------------------------------------
   /**
    * Reads the class license key ucfirstty
    *
    * @return string
    */
   function getKey()
   {
      return $this->key;
   }

   // ---------------------------------------------------------------------
   /**
    * Reads the license key from the database
    */
   function readKey()
   {
      //
      // You may want to use this method to read the license key from elsewhere
      // e.g. from a database with this pseudo code
      // $this->key = read_key_from_db();
      //
   }
   
   // ---------------------------------------------------------------------
   /**
    * Saves the license key to the database
    */
   function saveKey($value)
   {
      //
      // You may want to use this method to save the license key elsewhere
      // e.g. to a database with this pseudo code
      // save_key_to_db($this->key);
      //
   }

   // ---------------------------------------------------------------------------
   /**
    * Sets the class license key ucfirstty
    *
    * @param string $key The license key
    */
   function setKey($key)
   {
      $this->key = $key;
   }
 
   // ---------------------------------------------------------------------------
   /**
    * Creates a table with license details and displays it inside a Bootstrap
    * alert box. This method assumes that your application uses Bootstrap 4.
    *
    * @param string $type  Type of information: notfound, invalid, details
    * @param array $data   License information array
    * @return string HTML
    */
   function show($data, $showDetails=false)
   {
      if (isset($data->result) && $data->result=="error")
      {
         $alert['type'] = 'danger';
         $alert['title'] = $this->lang['lic_invalid'];
         $alert['subject'] = $this->lang['lic_invalid_subject'];
         $alert['text'] = $this->lang['lic_invalid_text'];
         $alert['help'] = $this->lang['lic_invalid_help'];
         $details = "";
      }
      else
      {
         $domains = "";
         if (count($data->registered_domains)) {
            foreach ($data->registered_domains as $domain) {
               $domains .= $domain->registered_domain.', ';
            }
            $domains = substr($domains, 0, -2); // Remove last comma and blank
         }

         $daysleft = "";
         if ($daysToExpiry=$this->daysToExpiry()) {
            $daysleft = " (".$daysToExpiry." ".$this->lang['lic_daysleft'].")";
         }

         $details = "<div style=\"height:20px;\"></div>";
         $details .= "<table class=\"table table-hover\">
         <tr><th>".$this->lang['lic_product'].":</th><td>".$data->product_ref."</td></tr>
         <tr><th>".$this->lang['lic_key'].":</th><td>".$data->license_key."</td></tr>
         <tr><th>".$this->lang['lic_name'].":</th><td>".$data->first_name." ".$data->last_name."</td></tr>
         <tr><th>".$this->lang['lic_email'].":</th><td>".$data->email."</td></tr>
         <tr><th>".$this->lang['lic_company'].":</th><td>".$data->company_name."</td></tr>
         <tr><th>".$this->lang['lic_date_created'].":</th><td>".$data->date_created."</td></tr>
         <tr><th>".$this->lang['lic_date_renewed'].":</th><td>".$data->date_renewed."</td></tr>
         <tr><th>".$this->lang['lic_date_expiry'].":</th><td>".$data->date_expiry.$daysleft."</td></tr>
         <tr><th>".$this->lang['lic_registered_domains'].":</th><td>".$domains."</td></tr>
         </table>";

         switch ($this->status()) {
         
            case "active":
               $title = $this->lang['lic_active'];
               $alert['type'] = 'success';
               $alert['title'] = $title.'<span class="btn btn-'.$alert['type'].' btn-sm" style="margin-left:16px;">'.ucfirst($data->status).'</span>';
               $alert['subject'] = $this->lang['lic_active_subject'];
               $alert['text'] = '';
               $alert['help'] = '';
               break;

            case "expired":
               $title = $this->lang['lic_expired'];
               $alert['type'] = 'warning';
               $alert['title'] = $title.'<span class="btn btn-'.$alert['type'].' btn-sm" style="margin-left:16px;">'.ucfirst($data->status).'</span>';
               $alert['subject'] = $this->lang['lic_expired_subject'];
               $alert['help'] = $this->lang['lic_expired_help'];
               break;
   
            case "blocked":
               $alert['type'] = 'warning';
               $title = $this->lang['lic_blocked'];
               $alert['title'] = $title.'<span class="btn btn-'.$alert['type'].' btn-sm" style="margin-left:16px;">'.ucfirst($data->status).'</span>';
               $alert['subject'] = $this->lang['lic_blocked_subject'];
               $alert['text'] = '';
               $alert['help'] = $this->lang['lic_blocked_help'];
               break;
   
            case "pending":
               $alert['type'] = 'warning';
               $title = $this->lang['lic_pending'];
               $alert['title'] = $title.'<span class="btn btn-'.$alert['type'].' btn-sm" style="margin-left:16px;">'.ucfirst($data->status).'</span>';
               $alert['subject'] = $this->lang['lic_pending_subject'];
               $alert['text'] = '';
               $alert['help'] = $this->lang['lic_pending_help'];
               break;

            case "unregistered":
               $title = $this->lang['lic_active'];
               $alert['type'] = 'warning';
               $alert['title'] = $title.'<span class="btn btn-'.$alert['type'].' btn-sm" style="margin-left:16px;">'.ucfirst($data->status).'</span>';
               $alert['subject'] = $this->lang['lic_active_unregistered_subject'];
               $alert['text'] = '';
               $alert['help'] = '';
               break;
         }
      }

      $alertBox = '
         <div class="alert alert-dismissable alert-'.$alert['type'].'">
            <button type="button" class="close" data-dismiss="alert" title="'.$this->lang['close_this_message'].'"><i class="far fa-times-circle"></i></button>
            <h4><strong>'.$alert['title'].'</strong></h4>
            <hr>
            <p><strong>'.$alert['subject'].'</strong></p>
            <p>'.$alert['text'].'</p>
            '.(strlen($alert['help'])?"<p><i>".$alert['help']."</i></p>":"").(($showDetails)?$details:'').'
         </div>';

      return $alertBox;         
   }

   // ---------------------------------------------------------------------------
   /**
    * Get license status
    *
    * @return string  active/blocked/invalid/expired/pending/unregistered
    */
   function status()
   {
      if ($this->details->result=='error') return "invalid";

      switch ($this->details->status) {
         case "active":
            if (!$this->domainRegistered()) return 'unregistered';
            return 'active';
            break;

         case "expired":
            return 'expired';
            break;

         case "blocked":
            return 'blocked';
            break;

         case "pending":
            return 'pending';
            break;
      }
   }
}
?>
