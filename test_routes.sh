#!/bin/bash
ROUTES=("/" "/calendar" "/dashboard" "/login" "/mcp" "/profile" "/register" "/rooms" "/settings" "/sub-tenants" "/superadmin" "/superadmin/tenants" "/kiosk/jucu-hub" "/check-in/CFxyALWYDkccCsFMhgQg3LOH5G4AfOpp")
for ROUTE in "${ROUTES[@]}"; do
    STATUS=$(curl -b cookies.txt -s -o /dev/null -w "%{http_code}" http://localhost:8000$ROUTE)
    echo "$ROUTE: $STATUS"
done
