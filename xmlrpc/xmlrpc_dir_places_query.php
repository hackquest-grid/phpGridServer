<?php
/******************************************************************************
 * phpGridServer
 *
 * GNU LESSER GENERAL PUBLIC LICENSE
 * Version 2.1, February 1999
 *
 */

require_once("lib/services.php");
require_once("lib/types/RegionInfo.php");
require_once("lib/types/ServerDataURI.php");
require_once("lib/helpers/capabilityPathes.php");

if(count($_RPC_REQUEST->Params)!=1)
{
	return new RPCFaultResponse(4, "Missing struct parameter");
}

$structParam = $_RPC_REQUEST->Params[0];

if(!isset($structParam->text))
{
	return new RPCFaultResponse(4, "Missing parameter text");
}

if(!isset($structParam->flags))
{
	return new RPCFaultResponse(4, "Missing parameter flags");
}

if(!isset($structParam->category))
{
	return new RPCFaultResponse(4, "Missing parameter category");
}

if(!isset($structParam->query_start))
{
	return new RPCFaultResponse(4, "Missing parameter query_start");
}

$contentSearchService = getService("ContentSearch");

$rpcResponse = new RPCSuccessResponse();
$rpcData = new RPCStruct();
$rpcData->success = False;
$rpcData->errorMessage = "";
$rpcResponse->Params[] = $rpcData;
try
{
	$parcels = $contentSearchService->searchPlaces($structParam->text, $structParam->flags, $structParam->category, $structParam->query_start, 101);
	$rpcData->success = True;
}
catch(Exception $e)
{
	$rpcData->errorMessage = $e->getMessage();
	return $rpcResponse;
}


$data = array();
while($parcel = $parcels->getParcel())
{
	$rpcStruct = new RPCStruct();
	$rpcStruct->parcel_id = $parcel->InfoID;
	$rpcStruct->name = $parcel->Name;
	$rpcStruct->for_sale = $parcel->IsForSale ? "True":"False";
	$rpcStruct->auction = $parcel->IsAuction ? "True":"False";
	$rpcStruct->dwell = $parcel->Dwell;
	$data[] = $rpcStruct;
}
$parcels->free();
$rpcData->data = $data;

/* enable output compression */
if(!isset($_GET["rpc_debug"]))
{
	ini_set("zlib.output_compression", 4096);
}

return $rpcResponse;
