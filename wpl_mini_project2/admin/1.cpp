#include <iostream>
#include <vector>
#include <algorithm>
#include <cmath>

void LOOK(std::vector<int>& requests, int head) {
    int total = 0;
    std::sort(requests.begin(), requests.end());

    int idx = 0;
    for (int i = 0; i < requests.size(); i++) {
        if (requests[i] > head) {
            idx = i;
            break;
        }
    }

    std::cout << "\n[LOOK] Seek Sequence: " << head;

    for (int i = idx; i < requests.size(); i++) {
        std::cout << " -> " << requests[i];
        total += std::abs(head - requests[i]);
        head = requests[i];
    }

    for (int i = idx - 1; i >= 0; i--) {
        std::cout << " -> " << requests[i];
        total += std::abs(head - requests[i]);
        head = requests[i];
    }

    std::cout << "\nTotal Head Movement: " << total << "\n";
}

int main() {
    int n;
    std::cout << "Enter number of requests: ";
    std::cin >> n;

    std::vector<int> requests(n);
    std::cout << "Enter request sequence:\n";
    for (int i = 0; i < n; i++) {
        std::cin >> requests[i];
    }

    int head;
    std::cout << "Enter initial head position: ";
    std::cin >> head;

    LOOK(requests, head);

    return 0;
}
