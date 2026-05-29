# Ajouter un variant au Button shadcn

Le composant `Button` (`resources/js/components/ui/button/Button.vue`) utilise [class-variance-authority (cva)](https://cva.style/) pour générer ses classes Tailwind en fonction de props. Tout est centralisé dans `resources/js/components/ui/button/index.ts`.

## Anatomie de `index.ts`

```ts
export const buttonVariants = cva(
  "inline-flex items-center justify-center ...", // ① classes de base, toujours appliquées
  {
    variants: {
      variant: {                                  // ② dimension "couleur / style"
        default: "bg-primary text-primary-foreground ...",
        outline: "border bg-background ...",
        cta:     "rounded-none bg-[#8B2CF1] text-white shadow-[4px_4px_0_0_#4C1D95] ...",
      },
      size: {                                     // ③ dimension "taille"
        default: "h-9 px-4 py-2",
        lg:      "h-10 rounded-md px-6",
        cta:     "h-11 px-6 py-2.5",
      },
    },
    defaultVariants: { variant: "default", size: "default" },
  },
)
export type ButtonVariants = VariantProps<typeof buttonVariants>
```

`Button.vue` lit ce type pour typer ses props et concatène les classes via `cn()` (clsx + tailwind-merge).

## Ajouter un nouveau variant

1. Ouvrir `resources/js/components/ui/button/index.ts`.
2. Ajouter une entrée dans `variants.variant` (et/ou `variants.size`) :

   ```ts
   variant: {
     // ...
     ghost_hug: "bg-transparent text-[#8B2CF1] hover:bg-[#8B2CF1]/10",
   }
   ```

3. C'est tout. Le type `ButtonVariants["variant"]` est recalculé automatiquement, donc `<Button variant="ghost_hug">` est immédiatement type-safe.

## Utilisation

```vue
<Button variant="cta" size="cta">Créer une collecte</Button>

<!-- Avec un Link Inertia (rend le composant comme un <a>) -->
<Button as-child variant="cta" size="cta">
    <Link :href="routes.collecte.url()">Créer une collecte</Link>
</Button>
```

## Pourquoi pas un nouveau composant ?

Tant que c'est un bouton, on étend le `Button` existant. On crée un nouveau composant uniquement si le markup change (icône intégrée, structure différente, etc.). Le principe shadcn : tu **possèdes** le code source du composant, tu l'enrichis au lieu de le wrapper.

## Variants actuels du projet

| Variant       | Usage                                                            |
| ------------- | ---------------------------------------------------------------- |
| `default`     | Bouton standard sombre.                                          |
| `destructive` | Suppression / action irréversible.                               |
| `outline`     | Action secondaire avec bordure.                                  |
| `secondary`   | Action alternative discrète.                                     |
| `ghost`       | Action transparente (toolbars).                                  |
| `link`        | Style "lien souligné".                                           |
| `cta`         | CTA principal HUG (violet `#8B2CF1` + ombre offset). Réutilisé dans la navbar et la card "Accès collaborateur" du footer. |
