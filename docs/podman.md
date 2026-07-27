# Podman

Running pdflatex has security implications, which is why we strongly recommend the usage of a rootless podman container to run it. While this would mostly only be relevant if you are using unchecked input from users, it is still a good practice to run the compilation in a container which just has enough permissions to compile the document and cannot access anything else.

The bundle by default already uses the following security measures from pdflatex itself:

- `-no-shell-escape` (prevents shell commands from being executed)
- `-cnf-line="openin_any=p"` (prevent reads outside the working directory)
- `-cnf-line="openout_any=p"` (prevent writes outside the working directory)

But if another vector though pdflatex or bibtex is found, your local `.env` and/or secrets could be compromised.

## Building documents

Nothing special needs to be done to be able to use the podman based compiler, you just needs to make sure that all dependencies for the LaTeX document are installed in the working directory.

This can either be done by using the built-in `addDependency(string $dependency)` or `addLinkedDependency(string $dependency)` on the LaTeX document (`LatexBaseInterface`). Both methods will automatically place the dependency in the working directory during LaTeX file generation, with the difference being a symlink being used or not. Therefor, you can use the linked dependency to save some diskspace, although it will make the podman configuration more complex (more on this subject later).

## Running podman

We recommend the use of podman, as that does not need a daemon running with root privileges. The bundle offers two built-in methods to run podman:

- direct, using `Bobv\LatexBundle\Compiler\PodmanLatexCompiler`
- though proxy, using `Bobv\LatexBundle\Compiler\PodmanProxyLatexCompiler`

By default, we use the `registry.drenso.dev/open/docker-images/pdflatex:latest` container, which has been built using the instruction from this repository ([Dockerfile](https://gitlab.drenso.dev/open/docker-images/-/blob/main/pdflatex/latest/Dockerfile)).

Note that the direct option most probably will not work within your environment, especially if you are using FPM on Debian. By default, the FPM service is prohibited from mapping namespaces, preventing the container from being started. Next to that, it would still run with the permissions of the www-data user, which does not prevent reading the environment in case of potential breakout (which would need to be combined with a container breakout).

The podman container will automatically mount the working directory and the target directory of all linked dependencies. This means that the user running the podman container will need to have read permissions on those paths (and write permissions on the working directory).

### Running podman (direct)

> Note: while this runner already improves the separation of the pdflatex container from the host, it still runs with the permissions of the www-data user. This can be mitigated by using the proxy option.

Configure `Bobv\LatexBundle\Compiler\PodmanLatexCompiler` as `compiler.class` for the `bobv_latex` bundle. 

Additional options for this compiler are:

- `podman_homedir`: used to override the home directory of the user running the container (overrides where the container images are to be stored)
- `podman_binary_path`: used to override the path to the podman binary
- `podman_image`: used to override the used pdflatex image

### Running podman (proxy)

Configure `Bobv\LatexBundle\Compiler\PodmanProxyLatexCompiler` as `compiler.class` for the `bobv_latex` bundle. 

Additional options for this compiler are:

- `podman_proxy_sock`: Used to override the unix socket used to communicate with the podman proxy

This bundle offers a simple Python-based podman proxy that allows starting pdflatex and bibtex processes within a predefined container only. This proxy should be configured as a service using a dedicated user, where the user only has read permissions for the linked dependency directories and read/write permissions for the working directory in the cache (`%kernel.cache_dir%/%kernel.environment%/BobvLatex` by default).

For the rest of the guide we are going to use the `bobv-latex-pdflatex` user.

You can prepare the user by running the following commands:

```shell
apt install uidmap
adduser --disabled-password --gecos "User to execute pdflatex podman containers" bobv-latex-pdflatex
chmod o+rx /home/bobv-latex-pdflatex
```

Next, configure the service that will be running the proxy. You can use any method, a [systemd example is provided by us](../podman_proxy/bobv-latex-podman-proxy.conf).

Next, configure access to the directories the user needs to have access to. As example, we will provide some ACLs:

```shell
PARENT_DIRS=(
  "/var/www/project"
  "/var/www/project/var"
  "/var/www/project/var/cache"
  "/var/www/project/var/cache/prod"
)
LATEX_CACHE_DIR=/var/www/project/var/cache/prod/BobvLatex
DEPENDENCY_DIRS=(
  "/var/www/project/uploads",
  "/var/www/project/assets/pdf"
)

for PARENT_DIR in "${PARENT_DIRS[@]}"; do
  setfacl -nm u:bobv-latex-pdflatex:rx ${DEPENDENCY_DIR}
  setfacl -dnm u:bobv-latex-pdflatex:rx ${DEPENDENCY_DIR}  
done

setfacl -Rnm u:bobv-latex-pdflatex:rw ${LATEX_CACHE_DIR}
setfacl -dRnm u:bobv-latex-pdflatex:rw ${LATEX_CACHE_DIR}

for DEPENDENCY_DIR in "${DEPENDENCY_DIRS[@]}"; do
  setfacl -Rnm u:bobv-latex-pdflatex:r ${DEPENDENCY_DIR}
  setfacl -dRnm u:bobv-latex-pdflatex:r ${DEPENDENCY_DIR}
done
```

Finally, make sure the service is running and validate your installation by running `bin/console bobv:latex:test`.

## Deployment

During deployment, you will most probably want to update the proxy as well. Assuming you will use an `update` user to run deployment tasks, you can, for example, run the following:

```shell
# Install the latest podman proxy and restart it
sudo -n systemctl start bobv-latex-podman-proxy-installer
sudo -n systemctl restart bobv-latex-podman-proxy

# Pull latest pdflatex image and clear old images
sudo -n -u bobv-latex-pdflatex podman pull registry.drenso.dev/open/docker-images/pdflatex:latest
sudo -n -u bobv-latex-pdflatex podman image prune -f
```

To allow the sudo tasks, the following sudoers configuration can be used:

```sudoers
# /etc/sudoers.d/bobv-latex-pdflatex
update ALL= (root) NOPASSWD: /bin/systemctl start bobv-latex-podman-proxy-installer
update ALL= (root) NOPASSWD: /bin/systemctl restart bobv-latex-podman-proxy
update ALL= (bobv-latex-pdflatex) NOPASSWD: /usr/bin/podman pull registry.drenso.dev/open/docker-images/pdflatex\:latest
update ALL= (bobv-latex-pdflatex) NOPASSWD: /usr/bin/podman image prune -f
```

## Possible issues

#### I'm seeing warnings regarding

You can configure the following default in `/home/bobv-latex-pdflatex/.config/containers/containers.conf`:

```ini
[engine]
cgroup_manager = "cgroupfs"
```
