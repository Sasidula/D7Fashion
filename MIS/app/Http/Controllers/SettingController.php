<?php

namespace App\Http\Controllers;

use App\Models\ExternalProductItem;
use App\Models\InternalProductItem;
use App\Models\MaterialStock;

class SettingController extends Controller
{
    //delete material
    public function destroyDeletedMaterial()
    {
        $materials = MaterialStock::where('status', 'deleted')->get();

        foreach ($materials as $material) {
            $material->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Material deleted successfully.');
    }

    public function destroyUnavailableMaterial()
    {
        $materials = MaterialStock::where('status', 'unavailable')->get();

        foreach ($materials as $material) {
            $material->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Material deleted successfully.');
    }

    public function destroySoldInternalProduct()
    {
        $internalProducts = InternalProductItem::where('status', 'sold')->get();

        foreach ($internalProducts as $internalProduct) {
            $internalProduct->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Product deleted successfully.');
    }

    public function destroyDeletedInternalProduct()
    {
        $internalProducts = InternalProductItem::where('status', 'deleted')->get();

        foreach ($internalProducts as $internalProduct) {
            $internalProduct->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Product deleted successfully.');
    }

    public function destroyDeletedExternalProduct()
    {
        $externalProducts = ExternalProductItem::where('status', 'deleted')->get();

        foreach ($externalProducts as $externalProduct) {
            $externalProduct->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Product deleted successfully.');
    }

    public function destroySoldExternalProduct()
    {
        $externalProducts = ExternalProductItem::where('status', 'sold')->get();

        foreach ($externalProducts as $externalProduct) {
            $externalProduct->delete();
        }

        return redirect()->route('page.settings')->with('success', 'Product deleted successfully.');
    }

}
