<?php

namespace App\Http\Controllers\Administrator;

use App\Helper\FileUploader;
use App\Helper\ObbiAssets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\OBBI\obbiHelper;

use App\Model\Barang;
use App\Model\BarangGambar;

use DataTables;
use Session;
use File;

class BarangGambarController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, Builder $htmlBuilder) {
        if ( $request->ajax() ) {
            $datas = BarangGambar::with('barang')->get();

            return Datatables::of($datas)
                             ->addColumn('barang', function ($data) {
                                 return $data->barang['nama_barang'];
                             })
                             ->addColumn('gambar', function ($data) {
                                 return view('datatables._image', [
                                     'url' => ObbiAssets::get_asset(ObbiAssets::BARANG, $data->gambar_barang)
                                 ]);
                             })
                // obbiHelper::storage($data->gambar_barang)
                             ->addColumn('action', function ($data) {
                    return view('datatables._action', [
                        'model'           => $data,
                        'form_url'        => route('baranggambar.destroy', $data->id),
                        'edit_url'        => route('baranggambar.edit', $data->id),
                        'confirm_message' => 'Yakin mau menghapus ' . $data->barang->nama_barang . ' ?',

                    ]);

                })
                             ->rawColumns([ 'gambar', 'action' ])
                             ->make(true);
        }

        $html = $htmlBuilder
            ->addColumn([ 'data' => 'barang', 'name' => 'barang', 'title' => 'Nama Barang' ])
            ->addColumn([ 'data' => 'gambar', 'name' => 'gambar', 'title' => 'Gambar' ])
            ->addColumn([
                'data'       => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false,
                'searchable' => false
            ]);

        return view('administrator.baranggambar.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create () {
        return view('administrator.baranggambar.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request) {
        $this->validate($request, [
            'barang_id'     => 'required|exists:barangs,id',
            'gambar_barang' => 'required|mimes:jpeg,bmp,png,jpg'
        ], [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.',
            'numeric'  => 'Field :attribute harus berupa angka.',
            'mimes'    => 'Field :attribute bukan image.'
        ]);

        $upload = $helper = (new FileUploader(7559, FileUploader::BARANG, 'gambar_barang'))
            ->setMime([
                'image/jpeg', 'image/jpg', 'image/png'
            ])
            ->doUpload($request);

        if ( $upload['meta']['code'] == 200 ) {
            DB::beginTransaction();
            $data = new BarangGambar();
            $data->barang_id = $request->barang_id;

            $data->gambar_barang = $upload['data']['path'];
            $data->save();

            DB::commit();
            Session::flash("flash_notification", [
                "level"   => "success",
                "message" => $request->get('barang_id') . " successfully saved."
            ]);
        } else {

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be saved."
            ]);
        }

        return redirect()->route('baranggambar.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show ($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit ($id) {
        $data = BarangGambar::with('barang')->findOrFail($id);

        return view('administrator.baranggambar.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    //        public function update (Request $request, $id) {
    //            $rules = [
    //                'barang_id'     => 'required|exists:barangs,id',
    //                'gambar_barang' => 'required|mimes:jpeg,bmp,png,jpg',
    //            ];
    //
    //            $messages = [
    //                'required' => 'Field :attribute harus diisi.',
    //                'string'   => 'Field :attribute harus berupa karakter.',
    //                'exists'   => 'Field :attribute tidak ditemukan.',
    //                'numeric'  => 'Field :attribute harus berupa angka.',
    //                'mimes'    => 'Field :attribute bukan image.',
    //            ];
    //
    //            $this->validate($request, $rules, $messages);
    //
    //            DB::beginTransaction();
    //
    //            $data = BarangGambar::findOrFail($id);
    //
    //            if ( !empty($data->gambar_barang) ) {
    //                File::delete('storage' . $data->gambar_barang);
    //            }
    //
    //            if ( $request->has('gambar_barang') ) {
    //
    //                $file = $request->file('gambar_barang');
    //
    //                $extension = $file->getClientOriginalExtension();
    //                $fileName = md5(time()) . '.' . $extension;
    //                $destination = storage_path() . DIRECTORY_SEPARATOR . 'app/public' . DIRECTORY_SEPARATOR . 'barang/';
    //                $file->move($destination, $fileName);
    //
    //                $data->gambar_barang = '/barang/' . $fileName;
    //            }
    //
    //            if ( $data->update() ) {
    //                DB::commit();
    //
    //                Session::flash("flash_notification", [
    //                    "level"   => "success",
    //                    "message" => $request->get('barang_id') . " successfully updated."
    //                ]);
    //            } else {
    //                DB::rollBack();
    //
    //                Session::flash("flash_notification", [
    //                    "level"   => "warning",
    //                    "message" => "Data failed to be updated."
    //                ]);
    //            }
    //
    //            return redirect()->route('baranggambar.index');
    //        }
    public function update (Request $request, $id) {
        $this->validate($request, [
            'barang_id'     => 'required|exists:barangs,id',
            'gambar_barang' => 'required|mimes:jpeg,bmp,png,jpg',
        ], [
            'required' => 'Field :attribute harus diisi.',
            'string'   => 'Field :attribute harus berupa karakter.',
            'exists'   => 'Field :attribute tidak ditemukan.',
            'numeric'  => 'Field :attribute harus berupa angka.',
            'mimes'    => 'Field :attribute bukan image.',
        ]);

        $data = BarangGambar::findOrFail($id);

        $upload = $helper = (new FileUploader(7559, FileUploader::BARANG, 'gambar_barang'))
            ->setMime([
                'image/jpeg', 'image/jpg', 'image/png'
            ])
            ->doUpload($request);
        $old = $data->gambar_barang;

        if ( $upload['meta']['code'] == 200 ) {
            DB::beginTransaction();
            $data->gambar_barang = $upload['data']['path'];

            if ( $data->update() ) {
                DB::commit();
                if ( !empty($data->gambar_barang) ) {
                    ObbiAssets::delete_asset(ObbiAssets::BARANG, $old);
                }
                Session::flash("flash_notification", [
                    "level"   => "success",
                    "message" => $request->get('barang_id') . " successfully updated."
                ]);
            } else {

                DB::rollBack();
                Session::flash("flash_notification", [
                    "level"   => "warning",
                    "message" => "Data failed to be updated."
                ]);
            }


        } else {

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be updated."
            ]);
        }

        return redirect()->route('baranggambar.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy (Request $request, $id) {
        DB::beginTransaction();

        $data = BarangGambar::find($id);

        if ( !$data->delete() ) {
            DB::rollBack();

            Session::flash("flash_notification", [
                "level"   => "warning",
                "message" => "Data failed to be deleted."
            ]);

            return redirect()->back();
        }
        if ( $request->ajax() )
            return response()->json([ 'id' => $id ]);


        DB::commit();
        ObbiAssets::delete_asset(ObbiAssets::BARANG, $data->gambar_barang);

        Session::flash("flash_notification", [
            "level"   => "success",
            "message" => "successfully deleted."
        ]);

        return redirect()->route('baranggambar.index');
    }
}
