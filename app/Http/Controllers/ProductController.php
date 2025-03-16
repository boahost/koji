<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'products' => Product::with(['category', 'department'])->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all(),
            'departments' => Department::all()
        ]);
    }

    public function store(Request $request)
    {
        // Formata o preço antes da validação
        $price = $request->price;
        if (is_string($price)) {
            $price = str_replace(['R$', ' '], '', $price);
            $price = str_replace('.', '', $price);
            $price = str_replace(',', '.', $price);
        }

        $request->merge(['price' => $price]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'images.*' => ['required', 'image', 'max:2048'],
            'featured_image' => ['required', 'integer', 'min:0']
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
        }

        $validated['images'] = $images;
        $validated['featured_image'] = $images[$request->featured_image] ?? ($images[0] ?? null);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => array_merge($product->toArray(), [
                'images' => $product->images ?? [],
                'featured_image' => $product->featured_image ?? 0
            ]),
            'categories' => Category::all(),
            'departments' => Department::all()
        ]);
    }

    public function update(Request $request, Product $product)
    {

        try {
            // Formata o preço
            $price = $request->input('price');
            if (is_string($price)) {
                $price = str_replace(['R$', ' '], '', $price);
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
            }

            // Prepara os dados para atualização
            $data = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $price,
                'category_id' => $request->input('category_id'),
                'department_id' => $request->input('department_id')
            ];

            // Mantém as imagens existentes
            $images = $product->images ?? [];

            // Processa novas imagens
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Valida cada imagem
                    $validator = Validator::make(
                        ['image' => $image],
                        ['image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120']],
                        [
                            'image.image' => 'O arquivo não é uma imagem válida.',
                            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg ou gif.',
                            'image.max' => 'A imagem não pode ser maior que 5MB.'
                        ]
                    );

                    if ($validator->fails()) {
                        throw new \Exception($validator->errors()->first());
                    }

                    $path = $image->store('products', 'public');
                    if (!$path) {
                        throw new \Exception('Erro ao salvar uma ou mais imagens. Tente novamente.');
                    }
                    $images[] = $path;
                }
            }

            // Atualiza as imagens e a imagem destacada
            $data['images'] = $images;
            $data['featured_image'] = $request->input('featured_image', 0);

            // Atualiza o produto
            $product->update($data);

            return back()->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar produto: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar produto: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function updateImages(Request $request, Product $product)
    {
        try {
            Log::info('Iniciando upload de imagens', [
                'request_data' => $request->all(),
                'files' => $request->files->all()
            ]);

            // Inicializa o array de imagens com as imagens existentes
            $images = $product->images ?? [];
            
            // Processa novas imagens se houver
            if ($request->hasFile('new_images')) {
                $files = $request->file('new_images');
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $index => $image) {
                    if (!$image->isValid()) {
                        throw new \Exception('A imagem #' . ($index + 1) . ' está corrompida ou é inválida.');
                    }

                    $validator = Validator::make(
                        ['image' => $image],
                        ['image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']],
                        [
                            'image.required' => 'A imagem #' . ($index + 1) . ' é obrigatória.',
                            'image.image' => 'O arquivo #' . ($index + 1) . ' não é uma imagem válida.',
                            'image.mimes' => 'A imagem #' . ($index + 1) . ' deve ser do tipo: jpeg, png, jpg ou gif.',
                            'image.max' => 'A imagem #' . ($index + 1) . ' não pode ser maior que 2MB.'
                        ]
                    );

                    if ($validator->fails()) {
                        throw new \Exception($validator->errors()->first());
                    }

                    $path = $image->store('products', 'public');
                    if (!$path) {
                        throw new \Exception('Erro ao salvar uma ou mais imagens. Tente novamente.');
                    }
                    $images[] = $path;
                }
            }
            
            // Remove imagens marcadas para remoção
            if ($request->has('remove_images') && !empty($request->remove_images)) {
                $removeIndexes = json_decode($request->remove_images);
                if (is_array($removeIndexes)) {
                    foreach ($removeIndexes as $index) {
                        if (isset($images[$index])) {
                            Storage::disk('public')->delete($images[$index]);
                            unset($images[$index]);
                        }
                    }
                    // Reindexar o array após remover imagens
                    $images = array_values($images);
                }
            }

            // Atualiza o produto com as novas imagens
            $product->images = $images;
            $featured_index = $request->input('featured_image', 0);
            $product->featured_image = isset($images[$featured_index]) ? $images[$featured_index] : ($images[0] ?? null);
            $product->save();

            // Prepara as URLs completas das imagens
            $imageUrls = array_map(function($path) {
                return Storage::disk('public')->url($path);
            }, $images);

            return back()->with('flash', [
                'message' => 'Imagens atualizadas com sucesso!',
                'images' => $images,
                'imageUrls' => $imageUrls,
                'featured_image' => $product->featured_image
            ]);
        } catch (\Exception $e) {
            // Remove qualquer imagem que possa ter sido salva em caso de erro
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            
            return back()->withErrors([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso!');
    }
}
