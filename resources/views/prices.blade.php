<!DOCTYPE html>
<html lang="fr" data-theme="spotin">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spot in — Options de Prix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-200 min-h-screen">

{{-- Navbar --}}
<div class="navbar bg-base-100 shadow-sm sticky top-0 z-50 px-4 lg:px-12">
    <div class="navbar-start">
        <a href="/" class="text-xl font-bold">Spot <span class="text-primary">in</span></a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal gap-1 text-sm font-medium">
            <li><a href="/">Solution</a></li>
            <li><a class="text-primary font-semibold">Tarifs</a></li>
        </ul>
    </div>
    <div class="navbar-end">
        <a href="mailto:info@spotin.ch" class="btn btn-primary btn-sm rounded-full px-5">Demander une démo</a>
    </div>
</div>

{{-- Page header --}}
<section class="pt-16 pb-10 text-center px-4">
    <h1 class="text-4xl font-extrabold italic mb-3">
        Options de <span class="text-primary">Prix</span>
    </h1>
    <p class="text-base-content/60 mb-8">Choisissez la catégorie qui correspond à votre organisation pour voir nos plans de spots.</p>

    {{-- Tabs --}}
    <div role="tablist" class="tabs tabs-box inline-flex mx-auto bg-base-100 rounded-full p-1 gap-1">
        <input type="radio" name="pricing_tabs" role="tab" class="tab rounded-full px-6 checked:bg-primary checked:text-primary-content font-medium" aria-label="Particulier / Asso / Entreprises" checked />
        <input type="radio" name="pricing_tabs" role="tab" class="tab rounded-full px-6 font-medium" aria-label="Administration publique" />
    </div>
</section>

{{-- Pricing table --}}
<section class="px-4 pb-16 max-w-5xl mx-auto">
    <div class="overflow-x-auto rounded-2xl shadow-sm">
        <table class="table table-md w-full bg-base-100">
            <thead>
                <tr class="bg-neutral text-neutral-content text-sm">
                    <th class="rounded-tl-2xl py-4 w-36">Nombre de spot Max</th>
                    <th class="py-4 w-32">Prix / an</th>
                    <th class="py-4 text-center w-24">Markdown</th>
                    <th class="py-4 text-center w-20">API</th>
                    <th class="py-4 text-center w-20">JSON</th>
                    <th class="py-4 text-center w-20">Stats</th>
                    <th class="py-4 text-center w-20">Logo</th>
                    <th class="py-4 text-center w-24">Partenaire</th>
                    <th class="py-4 text-center rounded-tr-2xl w-24">Expertise</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-base-200 text-sm">
                <tr>
                    <td class="font-semibold py-4">5</td>
                    <td class="text-primary font-bold">Gratuit</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">20</td>
                    <td class="font-medium">200.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">50</td>
                    <td class="font-medium">500.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold italic py-4">100</td>
                    <td class="font-medium">1000.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">200</td>
                    <td class="font-medium">1500.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">500</td>
                    <td class="font-medium">4000.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-base-content/30">–</td>
                    <td class="text-center text-base-content/30">–</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">1000</td>
                    <td class="font-medium">7000.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-primary font-bold">2h</td>
                </tr>
                <tr>
                    <td class="font-semibold py-4">10'000</td>
                    <td class="font-bold text-lg">15'000.–</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-success font-bold">✓</td>
                    <td class="text-center text-primary font-bold">10h</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

{{-- CTA card --}}
<section class="px-4 pb-20 max-w-2xl mx-auto">
    <div class="card bg-base-100 shadow-sm border border-base-300 rounded-3xl text-center p-10">
        <h2 class="text-2xl font-bold mb-3">Besoin d'une offre personnalisée ?</h2>
        <p class="text-base-content/60 mb-6 text-sm">
            Pour les projets de plus de 10'000 spots ou les demandes spécifiques, contactez-nous directement.
        </p>
        <div class="flex justify-center">
            <a href="mailto:info@spotin.ch" class="btn btn-neutral rounded-full px-8">Discuter avec un expert</a>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-neutral text-neutral-content px-6 py-10">
    <div class="max-w-5xl mx-auto flex flex-col lg:flex-row justify-between items-start gap-6">
        <div class="text-neutral-content/30 text-sm font-bold italic">Spot in</div>
        <div class="text-right">
            <p class="text-sm text-neutral-content/60 mb-1">Spot In® - Plateforme Suisse</p>
            <a href="mailto:info@spotin.ch" class="text-primary font-semibold hover:underline">info@spotin.ch</a>
        </div>
    </div>
    <div class="max-w-5xl mx-auto mt-8 pt-6 border-t border-neutral-content/10 flex flex-col lg:flex-row justify-between items-center gap-2 text-xs text-neutral-content/30">
        <p>© 2026 Spot in. Diffusion d'information sécurisée et souveraine.</p>
        <div class="flex gap-4">
            <a href="#" class="hover:text-neutral-content transition-colors">Conditions générales</a>
            <a href="#" class="hover:text-neutral-content transition-colors">Protection des données</a>
        </div>
    </div>
</footer>

</body>
</html>
