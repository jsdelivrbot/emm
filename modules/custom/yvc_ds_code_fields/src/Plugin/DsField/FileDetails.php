<?php

namespace Drupal\yvc_ds_code_fields\Plugin\DsField;

use Drupal\file\Entity\File;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the Title, filesize and filetype of uploaded Files.
 *
 * @DsField(
 *   id = "file_details",
 *   title = @Translation("DS: Files"),
 *   entity_type = "node",
 *   provider = "yvc_ds_code_fields",
 *   ui_limit = {"resource|*"}
 * )
 */
class FileDetails extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Custom Display Suite code field for showing Content Type label
    // on Collections, Initiatives and Resources
    // See:
    // https://www.sitepoint.com/custom-display-suite-fields-in-drupal-8/

    // Get current node
    $node = \Drupal::routeMatch()->getParameter('node');

    // Returnable variables
    $output = "";

    if (!$node->get('field_private_file')->isEmpty()) {
      $just_files = $node->get('field_private_file')->getValue();
      $header = "Files";
      $file_data = getResourceFileData($just_files, $header);
      $output .= $file_data;
    }
    if (!$node->get('field_customizable_files')->isEmpty()) {
      $customizable_files = $node->get('field_customizable_files')->getValue();
      $header = "Customizable Files";
      $file_data = getResourceFileData($customizable_files, $header);
      $output .= $file_data;
    }

    if (!$node->get('field_print_ready_files')->isEmpty()) {
      $print_ready_files = $node->get('field_print_ready_files')->getValue();
      $header = "Print Ready Files";
      $file_data = getResourceFileData($print_ready_files, $header);
      $output .= $file_data;
    }

    if (!empty($output)) {
      $licensing_agreement ="By downloading this resource I agree to the terms of the licensing agreement.";
      $output = $output . '<div class="file-licensing-agreement">' . $licensing_agreement . '</div>';
      return [
        '#markup' => $output,
      ];
    }
  }
}

function getResourceFileData($file_fields, $header) {
  $data = '<div class="file-group"><h3>' . $header . '</h3>';
  foreach ($file_fields as $k => $v){
    $fid = $v['target_id'];
    $file = File::load($fid);
    $description = $v['description'];
    $name = $file->getFilename();
    if (empty($description)) {
      $description = $name;
    }
    $size = formatBytes($file->getSize());
    $type = mime2ext($file->getMimeType());
    $uri = $file->getFileUri();
    $url = file_create_url($uri);
    $data .= '<div class="link-item"><a href="' . $url  . '"><div class="file-description">' . $description . '</div>';
    $data .= '<div class="file-size"><span class="file-header">Size:</span> ' . $size . '</div>';
    $data .= '<div class="file-type"><span class="file-header">File Type: </span>' . $type . '</div>';
    $data .= '</a></div>';
  }
  $data .= '</div>';
  return $data;
}

function formatBytes($bytes, $precision = 2) {
  $units = array('B', 'KB', 'MB', 'GB', 'TB');
  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);
  $bytes /= pow(1024, $pow);
  return round($bytes, $precision) . ' ' . $units[$pow];
}

function mime2ext($mime) {
  $all_mimes = '{
    "png":["image\/png","image\/x-png"],
    "bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],
    "gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],
    "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],
    "rtx":["text\/richtext"],
    "rtf":["text\/rtf"],
    "zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
    "mp4":["video\/mp4"],
    "m4a":["audio\/x-m4a"],
    "pdf":["application\/pdf","application\/octet-stream"],
    "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
    "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],
    "docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
    "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
    "xl":["application\/excel"],
    "xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
    "xsl":["text\/xsl"],
    "txt":["text\/plain"],
    "css":["text\/css"],
    "html":["text\/html"],
    "wav":["audio\/x-wav","audio\/wave","audio\/wav"],
    "xhtml":["application\/xhtml+xml"],
    "tar":["application\/x-tar"],
    "tgz":["application\/x-gzip-compressed"],
    "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],
    "bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],
    "wma":["audio\/x-ms-wma"],
    "vcf":["text\/x-vcard"],
    "srt":["text\/srt"],
    "vtt":["text\/vtt"],
    "ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],
    "csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],
    "json":["application\/json","text\/json"]}';
  $all_mimes = json_decode($all_mimes, TRUE);

  foreach ($all_mimes as $key => $value) {
    if (array_search($mime, $value) !== FALSE) {
      return $key;
    }
  }
  return FALSE;
}
