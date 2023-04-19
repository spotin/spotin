/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, SetMetadata } from '@nestjs/common';
import { ApiExtension } from '@nestjs/swagger';

export const Roles = (
  ...roles: string[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    SetMetadata('roles', roles),
    ApiExtension('x-roles', `[${roles.join(', ')}]`),
  );
