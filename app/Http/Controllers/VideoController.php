<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->get();
        return view('pages.Video.video', compact('videos'));
    }

    public function create()
    {
        return view('pages.Video.createvideo');
    }

    private function getThumbnail(string $url): string
    {
        if (preg_match('/(youtube\.com|youtu\.be)/i', $url)) {
            preg_match('/(?:v=|youtu\.be\/|embed\/|shorts\/)([a-zA-Z0-9_-]{11})/', $url, $yt);
            $id = $yt[1] ?? null;

            if ($id) {
                $maxres = "https://img.youtube.com/vi/{$id}/maxresdefault.jpg";
                $hq     = "https://img.youtube.com/vi/{$id}/hqdefault.jpg";

                try {
                    $check = Http::timeout(5)->head($maxres);
                    $thumbnail = ($check->successful() && ($check->header('Content-Length') ?? 0) > 2000)
                        ? $maxres
                        : $hq;
                } catch (\Exception $e) {
                    $thumbnail = $hq;
                }

                return $thumbnail;
            }
        }

        if (preg_match('/tiktok\.com/i', $url)) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get('https://www.tiktok.com/oembed', ['url' => $url]);

                if ($response->successful()) {
                    $thumb = $response->json('thumbnail_url');
                    if ($thumb) return $thumb;
                }
            } catch (\Exception $e) {
            }

            try {
                $response = Http::timeout(10)
                    ->get('https://noembed.com/embed', ['url' => $url]);

                if ($response->successful()) {
                    $thumb = $response->json('thumbnail_url');
                    if ($thumb) return $thumb;
                }
            } catch (\Exception $e) {
            }

            return 'https://placehold.co/400x300?text=TikTok';
        }

        if (preg_match('/instagram\.com/i', $url)) {
            try {
                $token = config('services.instagram.token'); // opsional
                $params = ['url' => $url, 'omitscript' => true];
                if ($token) $params['access_token'] = $token;

                $response = Http::timeout(10)
                    ->get('https://graph.facebook.com/v19.0/instagram_oembed', $params);

                if ($response->successful()) {
                    $thumb = $response->json('thumbnail_url');
                    if ($thumb) return $thumb;
                }
            } catch (\Exception $e) {
            }

            return 'https://placehold.co/400x300?text=Instagram';
        }

        return 'https://placehold.co/400x300?text=Video';
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'    => 'required',
            'deskripsi'=> 'nullable',
            'link'     => 'required|url',
        ]);

        Video::create([
            'judul'    => $request->judul,
            'tanggal'  => now(),
            'deskripsi'=> $request->deskripsi,
            'file_url' => $request->link,
            'thumbnail'=> $this->getThumbnail($request->link),
        ]);

        return redirect()->route('video.index')
            ->with('success', 'Video berhasil ditambah');
    }

    public function show($id)
    {
        $video = Video::findOrFail($id);
        return view('pages.Video.detailvideo', compact('video'));
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('pages.Video.editvideo', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'    => 'required',
            'deskripsi'=> 'nullable',
            'link'     => 'required|url',
        ]);

        $video = Video::findOrFail($id);
        $video->update([
            'judul'    => $request->judul,
            'deskripsi'=> $request->deskripsi,
            'file_url' => $request->link,
            'thumbnail'=> $this->getThumbnail($request->link),  // update thumbnail juga
        ]);

        return redirect()->route('video.index')
            ->with('success', 'Video berhasil diupdate');
    }

    public function destroy($id)
    {
        Video::findOrFail($id)->delete();
        return redirect()->route('video.index')
            ->with('success', 'Video berhasil dihapus');
    }
}