/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, Get as NestGet } from '@nestjs/common';
import { ApiOkResponse, ApiOperation } from '@nestjs/swagger';

type GetManyDecoratorOptions = {
  path?: string;
  name: string;
  summary: string;
  description?: string;
  responseType: Function | [Function];
  operationId: string;
};

export const GetMany = ({
  path,
  name,
  summary,
  description,
  responseType,
  operationId,
}: GetManyDecoratorOptions) =>
  applyDecorators(
    NestGet(path ?? ''),
    ApiOperation({
      summary,
      description: description ?? `${summary}.`,
      operationId,
    }),
    ApiOkResponse({
      description: `${name} have been successfully retrieved.`,
      type: responseType,
    }),
  );
