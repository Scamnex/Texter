<?php

/**
 * // English
 *
 * Texter, the display FloatingTextPerticle plugin for PocketMine-MP
 * Copyright (c) 2018 yuko fuyutsuki < https://github.com/fuyutsuki >
 *
 * This software is distributed under "MIT license".
 * You should have received a copy of the MIT license
 * along with this program.  If not, see
 * < https://opensource.org/licenses/mit-license >.
 *
 * ---------------------------------------------------------------------
 * // 日本語
 *
 * TexterはPocketMine-MP向けのFloatingTextPerticleを表示するプラグインです
 * Copyright (c) 2018 yuko fuyutsuki < https://github.com/fuyutsuki >
 *
 * このソフトウェアは"MITライセンス"下で配布されています。
 * あなたはこのプログラムと共にMITライセンスのコピーを受け取ったはずです。
 * 受け取っていない場合、下記のURLからご覧ください。
 * < https://opensource.org/licenses/mit-license >
 */

declare(strict_types = 1);

namespace tokyo\pmmp\Texter\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use tokyo\pmmp\Texter\Core;

/**
 * CheckUpdateTaskClass
 */
class CheckUpdateTask extends AsyncTask {

  public function onRun(): void {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.github.com/repos/fuyutsuki/Texter/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => "php_".PHP_VERSION,
      CURLOPT_SSL_VERIFYPEER => false
    ]);
    $json = curl_exec($curl);
    $errorNo = curl_errno($curl);
    if ($errorNo) {
      $error = curl_error($curl);
      throw new \Exception($error);
    }
    curl_close($curl);
    $data = json_decode($json, true);
    $this->setResult($data);
  }

  public function onCompletion(): void {
    $core = Core::get();
    if ($core->isEnabled()) {
      $data = $this->getResult();
      if (isset($data[0])) {
        $ver = new VersionString($data[0]["name"]);
        $core->compareVersion(true, $ver, $data[0]["html_url"]);
      }else {
        $core->compareVersion(false);
      }
    }
  }
}
