<!-- Cài k6
sudo apt update
sudo apt install -y gnupg software-properties-common

sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://dl.k6.io/key.gpg | sudo gpg --dearmor -o /etc/apt/keyrings/k6-archive-keyring.gpg

echo "deb [signed-by=/etc/apt/keyrings/k6-archive-keyring.gpg] https://dl.k6.io/deb stable main" | sudo tee /etc/apt/sources.list.d/k6.list

sudo apt update
sudo apt install k6 -->
