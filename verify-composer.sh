#!/bin/bash
# Script to verify composer.lock on the server

echo "🔍 Vérification du composer.lock sur le serveur"
echo ""

echo "1️⃣ Vérification de la version git actuelle:"
git log --oneline -3

echo ""
echo "2️⃣ Vérification du contenu de composer.lock (platform):"
grep -A5 '"platform"' composer.lock

echo ""
echo "3️⃣ Vérification de toutes les exigences PHP dans composer.lock:"
grep -n '"php":' composer.lock | head -20

echo ""
echo "4️⃣ Hash MD5 du composer.lock:"
md5sum composer.lock 2>/dev/null || md5 composer.lock

echo ""
echo "5️⃣ Date de dernière modification:"
ls -la composer.lock

echo ""
echo "📊 Analyse:"
if grep -q '"php": ">=8.2"' composer.lock; then
    echo "✅ Le composer.lock requiert PHP >= 8.2 (CORRECT)"
else
    echo "❌ Le composer.lock ne requiert PAS PHP >= 8.2"
    echo "Voici ce qui est trouvé:"
    grep '"php":' composer.lock | head -5
fi
