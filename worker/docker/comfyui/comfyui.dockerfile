FROM pytorch/pytorch:2.5.1-cuda12.4-cudnn9-runtime

RUN apt update && apt install -y git aria2

RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --break-system-packages \
        --upgrade pip wheel setuptools

RUN --mount=type=cache,target=/root/.cache/pip \
    pip install --break-system-packages \
        -r https://raw.githubusercontent.com/comfyanonymous/ComfyUI/master/requirements.txt \
        -r https://raw.githubusercontent.com/ltdrdata/ComfyUI-Manager/main/requirements.txt

COPY scripts/. /home/scripts/

EXPOSE 8188

ENV CLI_ARGS=""

CMD [ "bash", "/home/scripts/entrypoint.sh" ]
