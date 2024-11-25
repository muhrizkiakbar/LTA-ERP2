$stok_gudang = 100;
$rata3M = 40 * 10%;
$orderToko = 30;
$serve = 10;

if ($stok_gudang < $rata3M)
{
  if($rata3M > $orderToko)
  {
    $exDCFR = $serve / $rata3M;
  }
  else
  {
    $exDCFR = $serve / $orderToko;
  }
}