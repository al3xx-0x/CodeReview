#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

int main(void) {
    // The flag file location
    const char *flag_path = "/app/flag";

    // File pointer
    FILE *file;

    // Use setuid(0) to ensure the effective user ID is root (0)
    // This is good practice for SUID binaries, though often not strictly necessary
    // if the SUID bit is set correctly on the executable file.
    if (setuid(0) != 0) {
        perror("setuid failed");
        return 1;
    }

    // Open the flag file for reading
    file = fopen(flag_path, "r");

 if (file == NULL) {
        // If it fails to open, it's either missing or permissions are wrong.
        // For debugging the challenge, printing an error is useful.
        fprintf(stderr, "Error: Could not open flag file at %s\n", flag_path);
        return 1;
    }

    // Read and print the content character by character (or line by line)
    // We'll read line by line for cleaner output.
    char buffer[256];
    while (fgets(buffer, sizeof(buffer), file) != NULL) {
        printf("%s", buffer);
    }

    // Close the file
    fclose(file);

    return 0;
}