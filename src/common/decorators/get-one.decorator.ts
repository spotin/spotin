/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, Get as NestGet } from '@nestjs/common';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
} from '@nestjs/swagger';

type GetOneDecoratorOptions = {
  path?: string;
  name: string;
  summary: string;
  description?: string;
  responseType: Function | [Function];
  operationId: string;
};

export const GetOne = ({
  path,
  name,
  summary,
  description,
  responseType,
  operationId,
}: GetOneDecoratorOptions) =>
  applyDecorators(
    NestGet(path ?? ':id'),
    ApiOperation({
      summary,
      description: description ?? `${summary}.`,
      operationId,
    }),
    ApiParam({
      name: 'id',
      description: `The ${name} ID.`,
      format: 'uuid',
    }),
    ApiOkResponse({
      description: `The ${name} has been successfully retrieved.`,
      type: responseType,
    }),
    ApiNotFoundResponse({
      description: `${name} has not been found.`,
    }),
  );
