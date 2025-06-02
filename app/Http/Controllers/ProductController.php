<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProductController extends \Illuminate\Routing\Controller // Mude para o namespace completo
{

    public function __construct()
    {
        // Este middleware será aplicado a todos os métodos neste controller,
        // exceto 'index' e 'show'.
        // Se você quiser que TODAS as ações de produto (incluindo visualização)
        // sejam apenas para administradores, remova o ->except(...)
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('manage-products')) {
                abort(403, 'THIS ACTION IS UNAUTHORIZED.'); // Mensagem de erro para acesso não autorizado
            }
            return $next($request);
        })->except(['index', 'show']); // Permite que usuários não-admin vejam a lista e detalhes (se houver)

        // Se você quiser proteger métodos individualmente em vez de usar o construtor,
        // você pode chamar $this->authorize('manage-products'); no início de cada método protegido.
        // Exemplo:
        // public function create()
        // {
        //     $this->authorize('manage-products');
        //     return view('products.create');
        // }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all(); // Busca todos os produtos
        return view('products.index', compact('products')); // Passa os produtos para a view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create'); // Garanta que está retornando a view correta
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validatedData);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse // Usando Route Model Binding
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse // Altere de string $id para Product $product
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
